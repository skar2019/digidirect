<?php

namespace Digidirect\Pronto\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

class Inventory extends AbstractHelper
{
    // Configuration paths
    const CONFIG_PATH_URL = 'pronto_settings_section/pronto_group/url';
    const CONFIG_PATH_COMPCODE = 'pronto_settings_section/pronto_group/compcode';
    const CONFIG_PATH_USER = 'pronto_settings_section/pronto_group/user';
    const CONFIG_PATH_TOKEN = 'pronto_settings_section/pronto_group/token';

    // Sync settings
    const CHANGE_LOOKBACK_MINUTES = 20;
    const TIMEZONE = 'UTC';

    protected $curl;
    protected $jsonSerializer;
    protected $sourceItemsBySku;
    protected $sourceItemsSaveInterface;
    protected $sourceItemFactory;
    protected $productRepository;
    protected $productFactory;
    protected $stockRegistry;
    protected $logger;
    protected $scopeConfig;

    public function __construct(
        Curl $curl,
        JsonSerializer $jsonSerializer,
        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSaveInterface,
        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Digidirect\CustomInventoryLog\Logger\Logger $logger,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->curl = $curl;
        $this->jsonSerializer = $jsonSerializer;
        $this->sourceItemsBySku = $sourceItemsBySku;
        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
        $this->sourceItemFactory = $sourceItemFactory;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->stockRegistry = $stockRegistry;
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Main inventory enquiry - processes changed items
     */
    public function enquireInventory($startItem = 0)
    {
        $prontoFilter = $this->getChangeTimestamp();
        $json = $this->fetchInventoryData($startItem, $prontoFilter);

        if (!$this->isValidResponse($json)) {
            return true;
        }

        $lastCode = $this->processInventoryResponse($json);

        // Check if we've reached the end
        if ($startItem == $lastCode) {
            return true;
        }

        // Continue recursively
        return $this->enquireInventory($lastCode);
    }

    /**
     * Test version with verbose output
     */
    public function enquireInventoryTest($startItem = 0)
    {
        $prontoFilter = $this->getChangeTimestamp();
        echo "Filter timestamp: $prontoFilter<br/>";

        $json = $this->fetchInventoryData($startItem, $prontoFilter);

        if (!$this->isValidResponse($json)) {
            return true;
        }

        $lastCode = $this->processInventoryResponse($json, true);

        echo "Processed up to: $lastCode<br/>";
        exit; // For testing
    }

    /**
     * Get timestamp for change enquiry (20 minutes ago)
     */
    protected function getChangeTimestamp()
    {
        date_default_timezone_set(self::TIMEZONE);
        $time = strtotime('-' . self::CHANGE_LOOKBACK_MINUTES . ' minutes');
        return date('dmYHis', $time);
    }

    /**
     * Fetch inventory data from Pronto API
     */
    protected function fetchInventoryData($startItem, $prontoFilter)
    {
        $url = $this->buildInventoryUrl($startItem, $prontoFilter);

        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("Accept", "application/json");
        $this->setApiHeaders();

        $this->curl->setOption(CURLOPT_SSL_VERIFYHOST, false);
        $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->get($url);

        $result = $this->curl->getBody();
        return $this->jsonSerializer->unserialize($result);
    }

    /**
     * Build inventory API URL
     */
    protected function buildInventoryUrl($startItem, $prontoFilter)
    {
        $host = $this->scopeConfig->getValue(self::CONFIG_PATH_URL);

        return $host . '/rest/abtws/stock-master?' . http_build_query([
                'call-type' => 'change_enquiry',
                'check-warehouse-change' => 'Y',
                'date-time-change-min' => $prontoFilter,
                'check-price-change' => 'Y',
                'include-stock-movements' => 'Y',
                'start-item' => $startItem
            ]);
    }

    /**
     * Set API authentication headers
     */
    protected function setApiHeaders()
    {
        $this->curl->addHeader("compcode", $this->scopeConfig->getValue(self::CONFIG_PATH_COMPCODE));
        $this->curl->addHeader("user", $this->scopeConfig->getValue(self::CONFIG_PATH_USER));
        $this->curl->addHeader("token", $this->scopeConfig->getValue(self::CONFIG_PATH_TOKEN));
    }

    /**
     * Validate API response
     */
    protected function isValidResponse($json)
    {
        if (isset($json['response']['status']) && $json['response']['status'] == 'FAIL') {
            $this->logger->error('Pronto Inventory Sync', ['info' => $json['response']['message']]);
            return false;
        }

        return isset($json['stockmaster']);
    }

    /**
     * Process inventory response and update products
     */
    protected function processInventoryResponse($json, $verbose = false)
    {
        $lastCode = 0;

        // Determine if response is single product or multiple
        $products = $this->extractProducts($json);

        foreach ($products as $prodData) {
            if (!isset($prodData['code'])) {
                continue;
            }

            $lastCode = $prodData['code'];

            try {
                $this->updateInventoryProduct($prodData, $verbose);
            } catch (NoSuchEntityException $e) {
                $this->logProductNotFound($prodData['code'], $verbose);
            } catch (\Exception $e) {
                $this->logger->error('Error updating inventory for ' . $prodData['code'] . ': ' . $e->getMessage());
            }
        }

        return $lastCode;
    }

    /**
     * Extract products from response (handles both single and multiple product responses)
     */
    protected function extractProducts($json)
    {
        // Single product response
        if (isset($json['stockmaster']['stockcode']['code'])) {
            return [$json['stockmaster']];
        }

        // Multiple products response
        if (isset($json['stockmaster']['stockcode'])) {
            return $json['stockmaster']['stockcode'];
        }

        return [];
    }

    /**
     * Update product inventory and related data
     */
    protected function updateInventoryProduct(array $prodData, $verbose = false)
    {
        $sku = $prodData['code'];
        $forLogs = "SKU - $sku\n";

        if ($verbose) {
            echo "SKU - $sku<br/>";
        }

        $product = $this->productRepository->get($sku);

        // Update pricing
        $this->updateProductPricing($product, $prodData, $forLogs, $verbose);

        // Update status based on conditions
        $this->updateProductStatus($product, $prodData, $forLogs, $verbose);

        // Update special flags
        $this->updateProductFlags($product, $prodData, $forLogs, $verbose);

        // Update warehouse inventory
        $this->updateWarehouseInventory($product, $prodData, $forLogs, $verbose);

        // Set stock condition
        if (isset($prodData['stk-condition-code'])) {
            $product->setCustomAttribute('stock_condition', $prodData['stk-condition-code']);
        }

        // Save product
        $this->productRepository->save($product);

        if (!$verbose) {
            $this->logger->info($forLogs);
        }
    }

    /**
     * Update product pricing
     */
    protected function updateProductPricing($product, array $prodData, &$forLogs, $verbose = false)
    {
        // Get retail price
        $retail = $this->extractRetailPrice($prodData);

        if ($retail !== null) {
            $oldPrice = $product->getPrice();

            // Reset wiser_price if price changed
            if ($oldPrice != $retail) {
                $product->setCustomAttribute('wiser_price', '0');
            }

            $product->setPrice($retail);
            $forLogs .= "Price - $retail\n";

            if ($verbose) {
                echo "Price - $retail<br/>";
            }
        }

        // Get marketplace price
        $marketplacePrice = $this->extractMarketplacePrice($prodData);

        if ($marketplacePrice !== null) {
            $product->setCustomAttribute('marketplaces_price', $marketplacePrice ?: 0);
            $forLogs .= "Marketplace price - $marketplacePrice\n";

            if ($verbose) {
                echo "Marketplace price - $marketplacePrice<br/>";
            }
        }
    }

    /**
     * Extract retail price from product data
     */
    protected function extractRetailPrice(array $prodData)
    {
        if (isset($prodData['pricing']['price-region'][0]['prc-recommend-retail-inc-tax'])) {
            return $prodData['pricing']['price-region'][0]['prc-recommend-retail-inc-tax'];
        }

        if (isset($prodData['pricing']['price-region']['prc-recommend-retail-inc-tax'])) {
            return $prodData['pricing']['price-region']['prc-recommend-retail-inc-tax'];
        }

        return null;
    }

    /**
     * Extract marketplace price from product data
     */
    protected function extractMarketplacePrice(array $prodData)
    {
        if (isset($prodData['pricing']['price-region'][0]['prc-break-price-4-inc'])) {
            return $prodData['pricing']['price-region'][0]['prc-break-price-4-inc'] ?: 0;
        }

        if (isset($prodData['pricing']['price-region']['prc-break-price-4-inc'])) {
            return $prodData['pricing']['price-region']['prc-break-price-4-inc'] ?: 0;
        }

        return null;
    }

    /**
     * Update product status
     */
    protected function updateProductStatus($product, array $prodData, &$forLogs, $verbose = false)
    {
        $conditionCode = $prodData['stk-condition-code'] ?? '';
        $statusFlag = $prodData['stk-user-only-alpha4-1'] ?? '';

        if ($verbose) {
            echo "Status flag: $statusFlag<br/>";
        }

        // Obsolete products are always disabled
        if ($conditionCode === 'O') {
            $product->setStatus(Status::STATUS_DISABLED);
            $forLogs .= "Status: Disabled (Obsolete)\n";

            if ($verbose) {
                echo "Stock condition O - Disabled<br/>";
            }
            return;
        }

        // Handle other status flags
        switch ($statusFlag) {
            case '':
                // Empty flag - disable in production, skip in test
                if (!$verbose) {
                    $product->setStatus(Status::STATUS_DISABLED);
                    $forLogs .= "Status: Disabled (No flag)\n";
                }
                break;

            case 'N':
                $product->setStatus(Status::STATUS_DISABLED);
                $forLogs .= "Status: Disabled (N flag)\n";

                if ($verbose) {
                    echo "Disable N<br/>";
                }
                break;

            case 'W':
                // Web enabled, but check NDA
                if ($product->getIsNda()) {
                    $product->setStatus(Status::STATUS_DISABLED);
                    $forLogs .= "Status: Disabled (NDA)\n";

                    if ($verbose) {
                        echo "Disable NDA<br/>";
                    }
                } else {
                    $product->setStatus(Status::STATUS_ENABLED);
                    $forLogs .= "Status: Enabled\n";

                    if ($verbose) {
                        echo "Enabled<br/>";
                    }
                }
                break;

            default:
                // No status change
                if ($verbose) {
                    echo "No status change<br/>";
                }
                break;
        }
    }

    /**
     * Update product flags (awaiting, pre-order)
     */
    protected function updateProductFlags($product, array $prodData, &$forLogs, $verbose = false)
    {
        $statusFlag = $prodData['stk-user-only-alpha4-1'] ?? '';

        // Awaiting product flag
        if ($statusFlag === 'A') {
            $product->setCustomAttribute('awaiting_product', '1');
            $forLogs .= "Awaiting: 1\n";

            if ($verbose) {
                echo "Awaiting 1<br/>";
            }
        } else {
            $product->setCustomAttribute('awaiting_product', '0');
            $forLogs .= "Awaiting: 0\n";

            if ($verbose) {
                echo "Awaiting 0<br/>";
            }
        }

        // Pre-order flag
        if ($statusFlag === 'P') {
            $product->setCustomAttribute('pre_order', '1');
            $product->setCustomAttribute('preorder', '1');
            $forLogs .= "Pre-order: 1\n";

            if ($verbose) {
                echo "Pre Order 1<br/>";
            }
        }
    }

    /**
     * Update warehouse inventory
     */
    protected function updateWarehouseInventory($product, array $prodData, &$forLogs, $verbose = false)
    {
        if (!isset($prodData['warehouse']['whse'])) {
            return;
        }

        $warehouses = $prodData['warehouse']['whse'];

        // Handle single warehouse vs multiple warehouses
        if (!$this->isArrayOfArrays($warehouses)) {
            $warehouses = [$warehouses];
        }

        foreach ($warehouses as $warehouse) {
            $this->updateSingleWarehouse($prodData['code'], $warehouse, $forLogs, $verbose);
        }
    }

    /**
     * Update inventory for a single warehouse
     */
    protected function updateSingleWarehouse($sku, array $warehouse, &$forLogs, $verbose = false)
    {
        $sourceCode = $warehouse['code'] ?? null;
        $quantity = $warehouse['qty_available'] ?? 0;

        if (!$sourceCode) {
            return;
        }

        $sourceItem = $this->sourceItemFactory->create();
        $sourceItem->setSourceCode($sourceCode);
        $sourceItem->setSku($sku);
        $sourceItem->setStatus(1);
        $sourceItem->setQuantity($quantity);

        $this->sourceItemsSaveInterface->execute([$sourceItem]);

        $forLogs .= "$sourceCode - $quantity\n";

        if ($verbose) {
            echo "$sourceCode - $quantity<br/>";
        }
    }

    /**
     * Check if array contains arrays (for multi-warehouse detection)
     */
    protected function isArrayOfArrays($array)
    {
        if (!is_array($array)) {
            return false;
        }

        foreach ($array as $item) {
            if (is_array($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Log product not found
     */
    protected function logProductNotFound($sku, $verbose = false)
    {
        $message = "SKU not exist - $sku";

        if ($verbose) {
            echo "$message<br/>";
        } else {
            $this->logger->info($message);
        }
    }

    /**
     * Get source items by SKU
     */
    public function getSourceItemBySku($sku)
    {
        return $this->sourceItemsBySku->execute($sku);
    }
}
