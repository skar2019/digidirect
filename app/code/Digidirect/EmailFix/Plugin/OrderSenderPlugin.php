<?php
declare(strict_types=1);

namespace Digidirect\EmailFix\Plugin;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
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
        'Sydney'       => 'SYDN',
        'Melbourne'    => 'MELB',
        'Parramatta'   => 'PARR',
        'Brisbane'     => 'BRIS',
        'Bondi'        => 'BOND',
        'Cannington'   => 'CANN',
        'Miranda'      => 'MIRA',
        'St Peters'    => 'SWHS',
        'St. Peters'   => 'SWHS',
        'Strathfield'  => '3WHS',
    ];

    /**
     * @var SourceRepositoryInterface
     */
    private SourceRepositoryInterface $sourceRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    ) {
        $this->sourceRepository      = $sourceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger                = $logger;
    }

    /**
     * After plugin on send() — injects pickup store name and address
     * into the order email transport variables.
     */
    public function beforeSend(OrderSender $subject, Order $order, bool $forceSyncMode = false): array
    {
        try {
            // Only process Click & Collect orders
            if ($order->getShippingMethod() !== 'collect_collect') {
                return [$order, $forceSyncMode];
            }

            $sourceCode     = null;
            $storeName      = 'digiDirect Store';
            $storeAddress   = '';

            // --- Step 1: Try to get source code from MSI pickup location table ---
            $connection = $this->getConnection();
            if ($connection) {
                $select = $connection->select()
                    ->from('inventory_pickup_location_order', ['pickup_location_code'])
                    ->where('order_id = ?', $order->getId());
                $sourceCode = $connection->fetchOne($select) ?: null;
            }

            // --- Step 2: Fallback — look up by city from shipping address ---
            if (!$sourceCode) {
                $shippingAddress = $order->getShippingAddress();
                if ($shippingAddress) {
                    $city = $shippingAddress->getCity();
                    $sourceCode = self::CITY_TO_SOURCE_MAP[$city] ?? null;
                }
            }

            // --- Step 3: Get store name from map ---
            if ($sourceCode && isset(self::STORE_NAME_MAP[$sourceCode])) {
                $storeName = self::STORE_NAME_MAP[$sourceCode];
            }

            // --- Step 4: Get store address from inventory_source table ---
            if ($sourceCode) {
                try {
                    $source = $this->sourceRepository->get($sourceCode);
                    $streetParts = array_filter([
                        $source->getStreet(),
                    ]);
                    $storeAddress = implode(', ', array_filter([
                        implode(' ', $streetParts),
                        $source->getCity(),
                        $source->getRegionId() ?: $source->getRegion(),
                        $source->getPostcode(),
                    ]));
                } catch (\Exception $e) {
                    $this->logger->warning('PickupEmailData: Could not load source ' . $sourceCode . ': ' . $e->getMessage());
                }
            }

            // --- Step 5: Fallback address from shipping address if source lookup failed ---
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

            // --- Step 6: Inject variables into order for template access ---
            $order->setData('pickup_store_name', $storeName);
            $order->setData('pickup_store_address', $storeAddress);

        } catch (\Exception $e) {
            $this->logger->error('PickupEmailData Plugin Error: ' . $e->getMessage());
        }

        return [$order, $forceSyncMode];
    }

    /**
     * Get DB connection from resource connection.
     */
    private function getConnection(): ?\Magento\Framework\DB\Adapter\AdapterInterface
    {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $resource = $objectManager->get(\Magento\Framework\App\ResourceConnection::class);
            return $resource->getConnection();
        } catch (\Exception $e) {
            return null;
        }
    }
}
