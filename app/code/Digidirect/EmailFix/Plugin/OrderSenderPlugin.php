<?php
declare(strict_types=1);

namespace Digidirect\EmailFix\Plugin;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class OrderSenderPlugin
{
    // Map of source codes to friendly store names
    private const STORE_NAME_MAP = [
        'SYDN' => 'digiDirect Sydney',
        'MELB' => 'digiDirect Melbourne',
        'PARR' => 'digiDirect Parramatta',
        'BRIS' => 'digiDirect Brisbane',
        'BOND' => 'digiDirect Bondi',
        'CANN' => 'digiDirect Cannington',
        'MIRA' => 'digiDirect Miranda',
        'SWHS' => 'digiDirect St. Peters',
        'RWHS' => 'digiDirect St Peters Refurbished',
        'MKPL' => 'digiDirect MarketPlacer',
        '3WHS' => 'digiDirect Strathfield',
    ];

    // Map of city names to source codes (fallback lookup)
    private const CITY_TO_SOURCE_MAP = [
        'Sydney'      => 'SYDN',
        'Melbourne'   => 'MELB',
        'Parramatta'  => 'PARR',
        'Brisbane'    => 'BRIS',
        'Bondi'       => 'BOND',
        'Cannington'  => 'CANN',
        'Miranda'     => 'MIRA',
        'St Peters'   => 'SWHS',
        'St. Peters'  => 'SWHS',
        'Strathfield' => '3WHS',
    ];

    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(
        SourceRepositoryInterface $sourceRepository,
        ResourceConnection $resourceConnection,
        LoggerInterface $logger
    ) {
        $this->sourceRepository   = $sourceRepository;
        $this->resourceConnection = $resourceConnection;
        $this->logger             = $logger;
    }

    /**
     * Before plugin — sets pickup store data on order so template can access it.
     * Also stores in registry for the around plugin to inject into transport vars.
     */
    public function beforeSend(OrderSender $subject, Order $order, bool $forceSyncMode = false): array
    {
        try {
            if ($order->getShippingMethod() !== 'collect_collect') {
                return [$order, $forceSyncMode];
            }

            [$storeName, $storeAddress] = $this->resolveStoreData($order);

            // Set on order object (accessible via {{var order.getData('pickup_store_name')}})
            $order->setData('pickup_store_name', $storeName);
            $order->setData('pickup_store_address', $storeAddress);

            // Also set on extension attributes for broader access
            $extensionAttributes = $order->getExtensionAttributes();
            if ($extensionAttributes) {
                if (method_exists($extensionAttributes, 'setPickupStoreName')) {
                    $extensionAttributes->setPickupStoreName($storeName);
                    $extensionAttributes->setPickupStoreAddress($storeAddress);
                    $order->setExtensionAttributes($extensionAttributes);
                }
            }

            $this->logger->info('PickupEmailData: Set store "' . $storeName . '" for order ' . $order->getIncrementId());

        } catch (\Exception $e) {
            $this->logger->error('PickupEmailData beforeSend Error: ' . $e->getMessage());
        }

        return [$order, $forceSyncMode];
    }

    /**
     * Around plugin — injects pickup_store_name and pickup_store_address
     * directly into the email template transport variables.
     */
    public function aroundSend(OrderSender $subject, callable $proceed, Order $order, bool $forceSyncMode = false): bool
    {
        try {
            if ($order->getShippingMethod() === 'collect_collect') {
                [$storeName, $storeAddress] = $this->resolveStoreData($order);

                // Use reflection to access protected templateContainer
                $reflection = new \ReflectionClass($subject);

                // Try to find templateContainer or similar property
                $properties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);

                foreach ($properties as $property) {
                    $property->setAccessible(true);
                    $value = $property->getValue($subject);

                    // Look for the template container object
                    if (is_object($value) && method_exists($value, 'setTemplateVars')) {
                        $existingVars = [];
                        if (method_exists($value, 'getTemplateVars')) {
                            $existingVars = $value->getTemplateVars() ?: [];
                        }
                        $existingVars['pickup_store_name']    = $storeName;
                        $existingVars['pickup_store_address'] = $storeAddress;
                        $value->setTemplateVars($existingVars);
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->warning('PickupEmailData aroundSend Warning: ' . $e->getMessage());
        }

        return $proceed($order, $forceSyncMode);
    }

    /**
     * Resolve store name and address for the order.
     *
     * @return array [string $storeName, string $storeAddress]
     */
    private function resolveStoreData(Order $order): array
    {
        $sourceCode   = null;
        $storeName    = 'digiDirect Store';
        $storeAddress = '';

        // Step 1: Try MSI pickup location order table
        try {
            $connection = $this->resourceConnection->getConnection();
            $tableName  = $this->resourceConnection->getTableName('inventory_pickup_location_order');
            $select     = $connection->select()
                ->from($tableName, ['pickup_location_code'])
                ->where('order_id = ?', $order->getId());
            $result     = $connection->fetchOne($select);
            if ($result) {
                $sourceCode = $result;
            }
        } catch (\Exception $e) {
            $this->logger->warning('PickupEmailData: MSI table lookup failed: ' . $e->getMessage());
        }

        // Step 2: Fallback — match by city from shipping address
        if (!$sourceCode) {
            $shippingAddress = $order->getShippingAddress();
            if ($shippingAddress) {
                $city       = $shippingAddress->getCity();
                $sourceCode = self::CITY_TO_SOURCE_MAP[$city] ?? null;
                $this->logger->info('PickupEmailData: City fallback used. City=' . $city . ', SourceCode=' . ($sourceCode ?? 'NOT FOUND'));
            }
        }

        // Step 3: Get friendly store name
        if ($sourceCode && isset(self::STORE_NAME_MAP[$sourceCode])) {
            $storeName = self::STORE_NAME_MAP[$sourceCode];
        }

        // Step 4: Get address from inventory_source table
        if ($sourceCode) {
            try {
                $source       = $this->sourceRepository->get($sourceCode);
                $storeAddress = implode(', ', array_filter([
                    $source->getStreet(),
                    $source->getCity(),
                    $source->getRegionId() ?: $source->getRegion(),
                    $source->getPostcode(),
                ]));
            } catch (\Exception $e) {
                $this->logger->warning('PickupEmailData: Source repository lookup failed for ' . $sourceCode . ': ' . $e->getMessage());
            }
        }

        // Step 5: Final fallback — use shipping address
        if (!$storeAddress) {
            $shippingAddress = $order->getShippingAddress();
            if ($shippingAddress) {
                $storeAddress = implode(', ', array_filter([
                    $shippingAddress->getStreetLine(1),
                    $shippingAddress->getCity(),
                    $shippingAddress->getRegion(),
                    $shippingAddress->getPostcode(),
                ]));
            }
        }

        return [$storeName, $storeAddress];
    }
}
