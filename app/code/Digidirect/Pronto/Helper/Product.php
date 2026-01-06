<?php

namespace Digidirect\Pronto\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Api\CategoryManagementInterface;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\CategoryLinkRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

class Product extends AbstractHelper
{
    const BRAND_ATTRIBUTE_CODE = 'brand';
    const DEFAULT_CATEGORY_PARENT_ID = 2;
    const MARKETPLACER_SELLER_ID = 20329;

    // Pronto API configuration paths
    const CONFIG_PATH_URL = 'pronto_settings_section/pronto_group/url';
    const CONFIG_PATH_COMPCODE = 'pronto_settings_section/pronto_group/compcode';
    const CONFIG_PATH_USER = 'pronto_settings_section/pronto_group/user';
    const CONFIG_PATH_TOKEN = 'pronto_settings_section/pronto_group/token';

    // Category name mappings
    const CATEGORY_MAPPINGS = [
        'Cameras' => 'Digital Cameras',
        'Gaming' => 'Gaming Products',
        'Fujifilm Instant Cameras' => 'Fujifilm Instant Instax Cameras',
        'Cables & Adaptors' => 'Computer Cables & Adaptors',
        'Cases Covers & Bags' => 'Laptop Cases, Covers & Bags',
        'Chargers' => 'Laptop Chargers',
        'Hubs & Docks' => 'Computer Hubs & Docks',
        'Webcams' => 'Computer Webcams',
        'Console Accessories' => 'Console Gaming Accessories',
        'Consoles' => 'Gaming Consoles',
        'Business' => 'Business Laptops',
        'Home & Student' => 'Home & Student Laptops',
        'Monitor Accessories' => 'Computer Monitor Accessories',
        'Monitor Mounts & Stands' => 'Monitor Arms, Mounts & Stands',
        'Monitors' => 'Computer Monitors',
        'Ink' => 'Printer Ink',
        'Paper' => 'Photo Printing Papers',
        'Shredders' => 'Paper Shredders',
        'Light Meters' => 'Light Meters for Cameras'
    ];

    // Brand name mappings
    const BRAND_MAPPINGS = [
        'thinktank' => 'think tank',
        'peak' => 'peak design',
        '3lt' => '3 legged thing'
    ];

    protected $curl;
    protected $jsonSerializer;
    protected $sourceItemsBySku;
    protected $sourceItemsSaveInterface;
    protected $sourceItemFactory;
    protected $productRepository;
    protected $productFactory;
    protected $stockRegistry;
    protected $logger;
    protected $productResource;
    protected $categoryFactory;
    protected $categoryLinkManagement;
    protected $categoryLinkRepository;
    protected $categoryManagement;
    protected $_reportCollectionFactory;
    protected $scopeConfig;
    protected $attributeOptions = [];
    protected $categoryList = [];

    public function __construct(
        Curl $curl,
        JsonSerializer $jsonSerializer,
        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSaveInterface,
        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Digidirect\CustomLog\Logger\Logger $logger,
        ProductResource $productResource,
        CategoryFactory $categoryFactory,
        CategoryLinkManagementInterface $categoryLinkManagement,
        CategoryLinkRepositoryInterface $categoryLinkRepository,
        CategoryManagementInterface $categoryManagement,
        \Magento\Reports\Model\ResourceModel\Product\Sold\CollectionFactory $reportCollectionFactory,
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
        $this->productResource = $productResource;
        $this->categoryFactory = $categoryFactory;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->categoryLinkRepository = $categoryLinkRepository;
        $this->categoryManagement = $categoryManagement;
        $this->_reportCollectionFactory = $reportCollectionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Main product sync entry point
     */
    public function productSync()
    {
        $startItem = 0;

        $this->logger->info('========================================');
        $this->logger->info('Starting Pronto Product Sync');
        $this->logger->info('Start item: ' . $startItem);
        $this->logger->info('========================================');

        $this->initializeSync();
        $this->logger->info('Initialization complete - brands and categories loaded');

        $this->logger->info('Fetching data from Pronto API...');
        $json = $this->fetchProntoData($startItem);

        // Log response structure
        $this->logger->info('API Response keys: ' . implode(', ', array_keys($json)));

        if (!isset($json['stockmaster']['stockcode'])) {
            $this->logger->error('Invalid response structure from Pronto API');
            if (isset($json['response'])) {
                $this->logger->error('Response message: ' . print_r($json['response'], true));
            }
            $this->logger->error('Full response structure: ' . print_r($json, true));
            return false;
        }

        $productCount = is_array($json['stockmaster']['stockcode']) ? count($json['stockmaster']['stockcode']) : 1;
        $this->logger->info('Found ' . $productCount . ' products to process');

        $lastCode = $this->processProducts($json['stockmaster']['stockcode']);

        $this->logger->info('Batch processing complete. Last code processed: ' . $lastCode);

        if (isset($json['response']['status']) && $json['response']['status'] == 'FAIL') {
            $this->logger->error('API returned FAIL status: ' . $json['response']['message']);
        }

        $this->logger->info('Continuing to next batch...');
        $this->productSyncContinue($lastCode);
    }

    /**
     * Continue sync from a specific item
     */
    public function productSyncContinue($startItem)
    {
        if (empty($startItem)) {
            $this->logger->info('========================================');
            $this->logger->info('Product sync complete - no more items to process');
            $this->logger->info('========================================');
            return true;
        }

        $this->logger->info('----------------------------------------');
        $this->logger->info('Continuing sync from item: ' . $startItem);

        $this->initializeSync();

        $this->logger->info('Fetching next batch from Pronto API...');
        $json = $this->fetchProntoData($startItem);

        // Log response structure
        if (isset($json['stockmaster'])) {
            $this->logger->info('Received stockmaster data');
        } else {
            $this->logger->warning('No stockmaster in response');
        }

        if (!isset($json['stockmaster']['stockcode'])) {
            $this->logger->info('No more products found - sync complete');
            $this->logger->info('========================================');
            return true;
        }

        $productCount = is_array($json['stockmaster']['stockcode']) ? count($json['stockmaster']['stockcode']) : 1;
        $this->logger->info('Found ' . $productCount . ' products in this batch');

        $lastCode = $this->processProducts($json['stockmaster']['stockcode']);

        $this->logger->info('Batch complete. Last code: ' . $lastCode);

        if (isset($json['response']['status']) && $json['response']['status'] == 'FAIL') {
            $this->logger->error('API returned FAIL status: ' . $json['response']['message']);
        }

        // Check if we've reached the end
        if ($startItem == $lastCode) {
            $this->logger->info('========================================');
            $this->logger->info('Sync complete - reached end of products');
            $this->logger->info('Final item: ' . $lastCode);
            $this->logger->info('========================================');
            return true;
        }

        $this->productSyncContinue($lastCode);
    }

    /**
     * Process a single product by SKU
     */
    public function productProntoSingle($sku)
    {
        set_time_limit(300);

        $this->initializeSync();
        $this->logger->info('Manual Pronto Product Sync - SKU: ' . $sku);

        echo "Starting sync for SKU: $sku<br/>\n";
        echo "Fetching data from Pronto API...<br/>\n";

        $json = $this->fetchProntoData($sku, $sku);

        // Log the response structure for debugging
        $this->logger->info('API Response structure: ' . print_r(array_keys($json), true));
        echo "API Response Keys: " . implode(', ', array_keys($json)) . "<br/>\n";

        if (!isset($json['stockmaster'])) {
            $this->logger->error('No stockmaster in response for SKU: ' . $sku);
            $this->logger->error('Full response: ' . print_r($json, true));
            echo "ERROR: No stockmaster in response<br/>\n";
            echo "Full response: <pre>" . print_r($json, true) . "</pre><br/>\n";
            return false;
        }

        // Log stockmaster structure
        if (is_array($json['stockmaster'])) {
            $this->logger->info('Stockmaster keys: ' . print_r(array_keys($json['stockmaster']), true));
            echo "Stockmaster Keys: " . implode(', ', array_keys($json['stockmaster'])) . "<br/>\n";
        }

        // Handle different response structures
        $products = [];

        if (isset($json['stockmaster']['stockcode'])) {
            $this->logger->info('Found stockcode in response');
            echo "Response type: stockcode format<br/>\n";

            // Check if it's an array of products or single product
            if (is_array($json['stockmaster']['stockcode'])) {
                // Check if it's associative (single product) or indexed (multiple)
                if (isset($json['stockmaster']['stockcode']['code'])) {
                    // Single product in stockcode
                    $this->logger->info('Single product in stockcode array');
                    echo "Single product detected<br/>\n";
                    $products = [$json['stockmaster']['stockcode']];
                } elseif (isset($json['stockmaster']['stockcode'][0])) {
                    // Array of products
                    $this->logger->info('Multiple products in stockcode array');
                    echo "Multiple products detected<br/>\n";
                    $products = $json['stockmaster']['stockcode'];
                } else {
                    $this->logger->warning('Unexpected stockcode structure');
                    echo "WARNING: Unexpected stockcode structure<br/>\n";
                }
            }
        } elseif (isset($json['stockmaster']['code'])) {
            // Direct format (single product)
            $this->logger->info('Single product in direct format');
            echo "Response type: direct format<br/>\n";
            $products = [$json['stockmaster']];
        }

        if (empty($products)) {
            $this->logger->error('No valid product data found for SKU: ' . $sku);
            echo "ERROR: No valid product data found<br/>\n";
            return false;
        }

        $this->logger->info('Found ' . count($products) . ' product(s) to process');
        echo "Found " . count($products) . " product(s) to process<br/>\n";

        // Log first product structure for debugging
        if (isset($products[0])) {
            $this->logger->info('First product keys: ' . print_r(array_keys($products[0]), true));
            echo "Product data keys: " . implode(', ', array_keys($products[0])) . "<br/>\n";
        }

        $this->processProducts($products, true);

        echo "Sync completed<br/>\n";

        return true;
    }

    /**
     * Initialize sync prerequisites
     */
    protected function initializeSync()
    {
        $this->attributeOptions = $this->getOptionHash(self::BRAND_ATTRIBUTE_CODE);
        $this->categoryList = $this->getSubCategoryByParentID(self::DEFAULT_CATEGORY_PARENT_ID);
    }

    /**
     * Fetch data from Pronto API
     */
    protected function fetchProntoData($startItem, $endItem = null)
    {
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("Accept", "application/json");

        $url = $this->buildApiUrl($startItem, $endItem);
        $this->setApiHeaders();

        $this->curl->setOption(CURLOPT_SSL_VERIFYHOST, false);
        $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->get($url);

        $result = $this->curl->getBody();
        return $this->jsonSerializer->unserialize($result);
    }

    /**
     * Build API URL
     */
    protected function buildApiUrl($startItem, $endItem = null)
    {
        $host = $this->scopeConfig->getValue(self::CONFIG_PATH_URL);
        $url = $host . '/rest/abtws/stock-master?call-type=full_enquiry&start-item=' . $startItem;

        if ($endItem !== null) {
            $url .= '&end-item=' . $endItem;
        }

        return $url;
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
     * Process array of products
     */
    protected function processProducts(array $products, $verbose = false)
    {
        $lastCode = 0;
        $processedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        if ($verbose) {
            $this->logger->info('Processing ' . count($products) . ' products in verbose mode');
        } else {
            $this->logger->info('Processing batch of ' . count($products) . ' products');
        }

        foreach ($products as $prod) {
            if (!isset($prod['code'])) {
                $skippedCount++;
                $this->logger->warning('Product missing code field, skipping');
                continue;
            }

            $lastCode = $prod['code'];

            try {
                $this->updateProduct($prod, $verbose);
                $processedCount++;

                if ($verbose) {
                    $this->logger->info('✓ Successfully processed: ' . $prod['code']);
                }
            } catch (NoSuchEntityException $e) {
                if ($verbose) {
                    // In manual mode, create the product if it doesn't exist
                    $this->logger->info('Product not found, creating new: ' . $prod['code']);
                    echo "Product not found, creating new: " . $prod['code'] . "<br/>\n";
                    try {
                        $this->createProduct($prod, $verbose);
                        $processedCount++;
                    } catch (\Exception $createError) {
                        $errorCount++;
                        $this->logger->error('Error creating product ' . $prod['code'] . ': ' . $createError->getMessage());
                        echo "Error creating product: " . $createError->getMessage() . "<br/>\n";
                    }
                } else {
                    $skippedCount++;
                    $this->logger->info('Product not found in Magento, skipping: ' . $prod['code']);
                }
                continue;
            } catch (\Exception $e) {
                $errorCount++;
                $this->logger->error('Error processing product ' . $prod['code'] . ': ' . $e->getMessage());
                $this->logger->error('Stack trace: ' . $e->getTraceAsString());
            }
        }

        // Log summary
        $this->logger->info('Batch summary: Processed=' . $processedCount . ', Skipped=' . $skippedCount . ', Errors=' . $errorCount);

        return $lastCode;
    }

    /**
     * Update existing product
     */
    protected function updateProduct(array $prodData, $verbose = false)
    {
        $forLogs = "SKU " . $prodData['code'] . "\n";

        if ($verbose) {
            echo "SKU " . $prodData['code'] . "<br/>\n";
        }

        $product = $this->productRepository->get($prodData['code']);

        // Update basic product data
        $price = $this->updateProductPricing($product, $prodData, $forLogs, $verbose);
        $this->updateProductStatus($product, $prodData, $forLogs, $verbose);
        $this->updateProductBrand($product, $prodData, $forLogs, $verbose);
        $this->updateProductAttributes($product, $prodData, $forLogs, $verbose);

        // Calculate and set bestseller metric
        $productSales = $this->getProductSales($product->getId(), $price);
        $product->setCustomAttribute('nb_sales', $productSales);

        if ($verbose) {
            echo "Product Sales: $productSales<br/>\n";
        }

        // Set update date
        $product->setCustomAttribute('date_update', date('Y-m-d'));

        // Save product first (Magento requirement before category assignment)
        $this->productRepository->save($product);

        if ($verbose) {
            echo "Product saved<br/>\n";
        }

        // Update categories
        $this->updateProductCategories($product, $prodData, $forLogs, $verbose);

        $this->logger->info($forLogs);
    }

    /**
     * Update product pricing
     */
    protected function updateProductPricing($product, array $prodData, &$forLogs, $verbose = false)
    {
        $price = 0;

        // Get price from API
        if (isset($prodData['pricing']['price-region'][0]['prc-recommend-retail-inc-tax'])) {
            $price = $prodData['pricing']['price-region'][0]['prc-recommend-retail-inc-tax'];
        } elseif (isset($prodData['pricing']['price-region']['prc-recommend-retail-inc-tax'])) {
            $price = $prodData['pricing']['price-region']['prc-recommend-retail-inc-tax'];
        }

        // Update marketplaces price
        $marketplacesPrice = 0;
        if (isset($prodData['pricing']['price-region'][0]['prc-break-price-4-inc'])) {
            $marketplacesPrice = $prodData['pricing']['price-region'][0]['prc-break-price-4-inc'];
        } elseif (isset($prodData['pricing']['price-region']['prc-break-price-4-inc'])) {
            $marketplacesPrice = $prodData['pricing']['price-region']['prc-break-price-4-inc'];
        }

        $product->setCustomAttribute('marketplaces_price', $marketplacesPrice ?: 0);
        $forLogs .= "Marketplaces Price: $marketplacesPrice\n";

        if ($verbose) {
            echo "Price: $price<br/>\n";
            echo "Marketplaces Price: $marketplacesPrice<br/>\n";
        }

        return $price;
    }

    /**
     * Update product status based on conditions
     */
    protected function updateProductStatus($product, array $prodData, &$forLogs, $verbose = false)
    {
        $statusFlag = $prodData['stk-user-only-alpha4-1'] ?? '';
        $conditionCode = $prodData['stk-condition-code'] ?? '';

        if ($verbose) {
            echo "Status Flag: " . ($statusFlag ?: '(empty)') . "<br/>\n";
            echo "Condition Code: $conditionCode<br/>\n";
        }

        // Obsolete products are always disabled
        if ($conditionCode === 'O') {
            $product->setStatus(Status::STATUS_DISABLED);
            $forLogs .= "Status: Disabled (Obsolete)\n";

            if ($verbose) {
                echo "Status: Disabled (Obsolete)<br/>\n";
            }
            return;
        }

        // Check web flag
        if (empty($statusFlag) || $statusFlag === 'N') {
            $product->setStatus(Status::STATUS_DISABLED);
            $forLogs .= "Status: Disabled (No web flag)\n";

            if ($verbose) {
                echo "Status: Disabled (No web flag)<br/>\n";
            }
        } elseif ($statusFlag === 'W') {
            // Web enabled, but check NDA status
            if ($product->getIsNda()) {
                $product->setStatus(Status::STATUS_DISABLED);
                $forLogs .= "Status: Disabled (NDA)\n";

                if ($verbose) {
                    echo "Status: Disabled (NDA)<br/>\n";
                }
            } elseif ($conditionCode === 'O') {
                $product->setStatus(Status::STATUS_DISABLED);
                $forLogs .= "Status: Disabled (Obselete)\n";

                if ($verbose) {
                    echo "Status: Disabled (Obselete)<br/>\n";
                }
            } else {
                $product->setStatus(Status::STATUS_ENABLED);
                $forLogs .= "Status: Enabled\n";

                if ($verbose) {
                    echo "Status: Enabled<br/>\n";
                }
            }
        }

        // Set pre-order status
        if ($statusFlag === 'P') {
            $product->setCustomAttribute('pre_order_status', '1');
            $forLogs .= "Pre-order: Yes\n";

            if ($verbose) {
                echo "Pre-order: Yes<br/>\n";
            }
        }

        // Set awaiting product status
        if ($statusFlag === 'A') {
            $product->setCustomAttribute('awaiting_product', '1');
            $forLogs .= "Awaiting Product: Yes\n";

            if ($verbose) {
                echo "Awaiting Product: Yes<br/>\n";
            }
        } else {
            $product->setCustomAttribute('awaiting_product', '0');
        }
    }

    /**
     * Update product brand
     */
    protected function updateProductBrand($product, array $prodData, &$forLogs, $verbose = false)
    {
        // Determine brand name
        if (($prodData['stk-brand-desc'] ?? '') === 'digiSeconds') {
            $brandName = strtolower($prodData['d2brand'] ?? $prodData['stk-brand'] ?? '');
        } else {
            $brandName = strtolower($prodData['stk-brand'] ?? '');
        }

        // Apply brand mapping
        $brandName = self::BRAND_MAPPINGS[$brandName] ?? $brandName;

        // Set brand if it exists
        if (isset($this->attributeOptions[$brandName])) {
            $product->setBrand($this->attributeOptions[$brandName]);
            $forLogs .= "Brand: $brandName\n";

            if ($verbose) {
                echo "Brand: $brandName<br/>\n";
            }
        }
    }

    /**
     * Update various product attributes
     */
    protected function updateProductAttributes($product, array $prodData, &$forLogs, $verbose = false)
    {
        // Stock division attributes
        $this->setAttributeIfExists($product, 'stock_division', $prodData, 'stock-division');
        $this->setAttributeIfExists($product, 'stock_department', $prodData, 'stock-department');
        $this->setAttributeIfExists($product, 'stock_category', $prodData, 'stock-category');
        $this->setAttributeIfExists($product, 'stock_class', $prodData, 'stock-class');

        // DigiSeconds attributes
        $itemCondition = $prodData['stk-sort-analysis-code'] ?? $prodData['d2lvl1'] ?? ' ';
        $product->setCustomAttribute('item_condition', $itemCondition);

        $itemRating = $prodData['d2lvl2'] ?? ' ';
        $product->setCustomAttribute('item_rating', $itemRating);

        $d2Desc = $prodData['d2desc'] ?? ' ';
        $product->setCustomAttribute('d2desc', $d2Desc);

        $d2NewSku = $prodData['d2newsku'] ?? ' ';
        $product->setCustomAttribute('d2newsku', $d2NewSku);

        // Storage flags
        $storageFlag = $prodData['stk-storage-type-flag'] ?? '';
        $product->setCustomAttribute('dangerous_goods', $storageFlag === 'H' ? '1' : '0');
        $product->setCustomAttribute('bulky_item', $storageFlag === 'B' ? 1 : 0);

        if ($verbose && $storageFlag) {
            if ($storageFlag === 'H') {
                echo "Dangerous Goods: Yes<br/>\n";
            }
            if ($storageFlag === 'B') {
                echo "Bulky Item: Yes<br/>\n";
            }
        }

        // Qantas product flag
        if (isset($prodData['stk-user-only-alpha4-3'])) {
            $isQantas = $prodData['stk-user-only-alpha4-3'] === 'Q' ? '1' : '0';
            $product->setCustomAttribute('is_qantas_product', $isQantas);
        }

        // Marketplacer seller
        $product->setCustomAttribute('marketplacer_seller', self::MARKETPLACER_SELLER_ID);
    }

    /**
     * Helper to set attribute if exists in data
     */
    protected function setAttributeIfExists($product, $attributeCode, array $data, $dataKey)
    {
        if (isset($data[$dataKey])) {
            $product->setCustomAttribute($attributeCode, $data[$dataKey]);
        }
    }

    /**
     * Update product categories
     */
    protected function updateProductCategories($product, array $prodData, &$forLogs, $verbose = false)
    {
        $categoryIds = [];
        $catList = "";

        // Add digiSeconds categories
        if (($prodData['stock-division'] ?? '') === 'S') {
            $this->addCategoryByName('digiSeconds', $categoryIds, $catList);
        }

        // Add condition categories
        $condition = $prodData['stk-sort-analysis-code'] ?? $prodData['d2lvl1'] ?? null;
        if (in_array($condition, ['OPENBOX', 'REFURB', 'PRELOVED', 'USED'])) {
            $categoryName = $condition === 'USED' ? 'PRELOVED' : $condition;
            $this->addCategoryByName($categoryName, $categoryIds, $catList);
        }

        // Add web categories (hierarchical)
        $this->addWebCategories($prodData, $categoryIds, $catList);

        $forLogs .= "Categories: $catList\n";

        if ($verbose) {
            echo "Categories: $catList<br/>\n";
        }

        // Assign categories
        if (count($categoryIds)) {
            try {
                $this->categoryLinkManagement->assignProductToCategories($prodData['code'], $categoryIds);

                if ($verbose) {
                    echo "Categories assigned successfully<br/>\n";
                }
            } catch (\Exception $e) {
                $this->logger->error('Failed to assign categories for ' . $prodData['code'] . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * Add web categories hierarchically
     */
    protected function addWebCategories(array $prodData, array &$categoryIds, &$catList)
    {
        $parent = '';
        $subcat1 = '';
        $subcat2 = '';

        // Level 1 (top level category)
        if (isset($prodData['web-category1'])) {
            $cat1 = $this->mapCategoryName($prodData['web-category1']);
            foreach ($this->categoryList as $category) {
                if ($category['name'] === $cat1 && $category['parent_id'] == self::DEFAULT_CATEGORY_PARENT_ID) {
                    $parent = $category['id'];
                    $categoryIds[] = $category['id'];
                    $catList .= $category['name'] . ' - ' . $category['id'] . ' : ';
                    break;
                }
            }
        }

        // Level 2
        if (isset($prodData['web-category2']) && $parent) {
            $cat2 = $this->mapCategoryName($prodData['web-category2']);
            foreach ($this->categoryList as $category) {
                if ($category['name'] === $cat2 && $category['parent_id'] == $parent) {
                    $subcat1 = $category['id'];
                    $categoryIds[] = $category['id'];
                    $catList .= $category['name'] . ' - ' . $category['id'] . ' : ';
                    break;
                }
                // Special case for Camera Cases and Bags
                if ($category['name'] === 'Camera Cases and Bags' && $cat2 === 'Bags & Cases' && $category['parent_id'] == $parent) {
                    $subcat1 = $category['id'];
                    $categoryIds[] = $category['id'];
                    $catList .= $category['name'] . ' - ' . $category['id'] . ' : ';
                    break;
                }
            }
        }

        // Level 3
        if (isset($prodData['web-category3']) && $subcat1) {
            $cat3 = $this->mapCategoryName($prodData['web-category3']);
            foreach ($this->categoryList as $category) {
                if ($category['name'] === $cat3 && $category['parent_id'] == $subcat1) {
                    $subcat2 = $category['id'];
                    $categoryIds[] = $category['id'];
                    $catList .= $category['name'] . ' - ' . $category['id'] . ' : ';
                    break;
                }
            }
        }

        // Level 4
        if (isset($prodData['web-category4']) && $subcat2) {
            $cat4 = $this->mapCategoryName($prodData['web-category4']);
            foreach ($this->categoryList as $category) {
                if ($category['name'] === $cat4 && $category['parent_id'] == $subcat2) {
                    $categoryIds[] = $category['id'];
                    $catList .= $category['name'] . ' - ' . $category['id'] . ' : ';
                    break;
                }
            }
        }
    }

    /**
     * Add category by name to the list
     */
    protected function addCategoryByName($categoryName, array &$categoryIds, &$catList)
    {
        foreach ($this->categoryList as $category) {
            if ($category['name'] === $categoryName) {
                $categoryIds[] = $category['id'];
                $catList .= $category['name'] . ' - ' . $category['id'] . ' : ';
                break;
            }
        }
    }

    /**
     * Map category names based on predefined mappings
     */
    protected function mapCategoryName($categoryName)
    {
        return self::CATEGORY_MAPPINGS[$categoryName] ?? $categoryName;
    }

    /**
     * Get product sales data for bestseller calculation
     */
    public function getProductSales($entityId, $price)
    {
        $soldProducts = $this->_reportCollectionFactory->create();
        $soldProductCol = $soldProducts->addOrderedQty(
            date('Y-m-d', strtotime('-30 days')),
            date('Y-m-d')
        )->addAttributeToFilter('product_id', $entityId);

        if (!$soldProductCol->count()) {
            return 0;
        }

        $product = $soldProductCol->getFirstItem();
        $productSales = (int)$product->getData('ordered_qty') * $price;

        $this->logger->info(sprintf(
            'getProductSales: ID=%s, Qty=%s, Price=%s, Total=%s',
            $entityId,
            $product->getData('ordered_qty'),
            $price,
            $productSales
        ));

        return $productSales;
    }

    /**
     * Get source items by SKU
     */
    public function getSourceItemBySku($sku)
    {
        return $this->sourceItemsBySku->execute($sku);
    }

    /**
     * Get attribute options as a hash map
     */
    protected function getOptionHash(string $attributeCode): array
    {
        $result = [];
        $attribute = $this->productResource->getAttribute($attributeCode);
        $options = $attribute->getSource()->getAllOptions(false);

        foreach ($options as $option) {
            if (!isset($option['value']) || !strlen($option['value'])) {
                continue;
            }
            $result[strtolower($option['label'])] = $option['value'];
        }

        return $result;
    }

    /**
     * Get all subcategories recursively
     */
    public function getSubCategoryByParentID(int $categoryId): array
    {
        $categoryData = [];
        $getSubCategory = $this->getCategoryData($categoryId);

        if (!$getSubCategory) {
            return $categoryData;
        }

        $this->extractCategoryData($getSubCategory->getChildrenData(), $categoryData);

        return $categoryData;
    }

    /**
     * Recursively extract category data
     */
    protected function extractCategoryData($categories, array &$categoryData, $depth = 0)
    {
        if ($depth > 4) { // Prevent infinite recursion
            return;
        }

        foreach ($categories as $category) {
            $categoryData[$category->getId()] = [
                'name' => $category->getName(),
                'url' => $category->getUrl(),
                'id' => $category->getId(),
                'parent_id' => $category->getParentId()
            ];

            if (count($category->getChildrenData())) {
                $subCategory = $this->getCategoryData($category->getId());
                if ($subCategory) {
                    $this->extractCategoryData($subCategory->getChildrenData(), $categoryData, $depth + 1);
                }
            }
        }
    }

    /**
     * Create new product from Pronto data
     */
    protected function createProduct(array $prodData, $verbose = false)
    {
        $forLogs = "Creating new product\n";
        $forLogs .= "SKU: " . $prodData['code'] . "\n";

        if ($verbose) {
            echo "Creating new product<br/>\n";
            echo "SKU: " . $prodData['code'] . "<br/>\n";
        }

        // Create product name
        $prodname = trim($prodData['desc1'] . " " . $prodData['desc2'] . " " . $prodData['desc3']);
        $forLogs .= "Product Name: $prodname\n";

        if ($verbose) {
            echo "Product Name: $prodname<br/>\n";
        }

        // Create new product
        $product = $this->productFactory->create();
        $product->setSku($prodData['code']);
        $product->setName($prodname);
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $product->setVisibility(4);
        $product->setAttributeSetId(4);
        $product->setMetaTitle($prodname);
        $product->setStatus(Status::STATUS_DISABLED); // New products start disabled

        // Create URL key
        $urlKey = preg_replace('/[+]/', 'plus', $prodname);
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $urlKey);
        $urlKey = strtolower($urlKey);
        $product->setUrlKey($urlKey);

        // Set pricing
        $price = 0;
        $tax = 10;
        if (isset($prodData['pricing']['price-region'][0]['prc-recommend-retail-inc-tax'])) {
            $price = $prodData['pricing']['price-region'][0]['prc-recommend-retail-inc-tax'];
            $tax = $prodData['pricing']['price-region'][0]['prc-tax-rate'] ?? 10;
        } elseif (isset($prodData['pricing']['price-region']['prc-recommend-retail-inc-tax'])) {
            $price = $prodData['pricing']['price-region']['prc-recommend-retail-inc-tax'];
            $tax = $prodData['pricing']['price-region']['prc-tax-rate'] ?? 10;
        }

        $product->setPrice($price);
        $forLogs .= "Price: $price\n";

        if ($verbose) {
            echo "Price: $price<br/>\n";
        }

        // Calculate cost
        $cost = $this->calculateProductCost($prodData, $price, $tax);
        $product->setCustomAttribute('cost', $cost);

        // Marketplace price
        $marketplacesPrice = 0;
        if (isset($prodData['pricing']['price-region'][0]['prc-break-price-4-inc'])) {
            $marketplacesPrice = $prodData['pricing']['price-region'][0]['prc-break-price-4-inc'] ?: 0;
        } elseif (isset($prodData['pricing']['price-region']['prc-break-price-4-inc'])) {
            $marketplacesPrice = $prodData['pricing']['price-region']['prc-break-price-4-inc'] ?: 0;
        }

        $product->setCustomAttribute('marketplaces_price', $marketplacesPrice);
        $forLogs .= "Marketplaces Price: $marketplacesPrice\n";

        if ($verbose) {
            echo "Marketplaces Price: $marketplacesPrice<br/>\n";
        }

        // Set brand
        $this->setProductBrand($product, $prodData, $forLogs, $verbose);

        // Set barcodes/GTINs
        $this->setProductBarcodes($product, $prodData, $forLogs, $verbose);

        // Set warehouse inventory
        $this->setWarehouseInventory($product, $prodData, $forLogs, $verbose);

        // Set Qantas attributes
        $product->setCustomAttribute('apn', $prodData['stk-apn-number'] ?? '');
        $product->setCustomAttribute('qff_base', $prodData['qff-base-points-per-dollar'] ?? 0);
        $product->setCustomAttribute('qff_bonus_points', $prodData['qff-bonus-points-per-dollar'] ?? 0);

        if (isset($prodData['qff-store-product-name'])) {
            $product->setCustomAttribute('qff_store_product_name', $prodData['qff-store-product-name']);
        }
        if (isset($prodData['qff-store-price'])) {
            $product->setCustomAttribute('qff_store_price', $prodData['qff-store-price']);
        }

        // DigiSeconds attributes
        $this->setDigiSecondsAttributes($product, $prodData);

        // Storage flags
        $storageFlag = $prodData['stk-storage-type-flag'] ?? '';
        $product->setCustomAttribute('dangerous_goods', $storageFlag === 'H' ? '1' : '0');
        $product->setCustomAttribute('bulky_item', $storageFlag === 'B' ? 1 : 0);

        // Stock division attributes
        $this->setAttributeIfExists($product, 'stock_division', $prodData, 'stock-division');
        $this->setAttributeIfExists($product, 'stock_department', $prodData, 'stock-department');
        $this->setAttributeIfExists($product, 'stock_category', $prodData, 'stock-category');
        $this->setAttributeIfExists($product, 'stock_class', $prodData, 'stock-class');

        // Set other attributes
        $product->setCustomAttribute('marketplacer_seller', self::MARKETPLACER_SELLER_ID);
        $product->setCustomAttribute('date_update', date('Y-m-d'));
        $product->setCustomAttribute('is_nda', 1); // New products start as NDA

        // Save product
        $this->productRepository->save($product);

        if ($verbose) {
            echo "Product created and saved<br/>\n";
        }

        // Set categories
        $this->updateProductCategories($product, $prodData, $forLogs, $verbose);

        $this->logger->info($forLogs);
    }

    /**
     * Calculate product cost from various sources
     */
    protected function calculateProductCost(array $prodData, $price, $tax)
    {
        $priceToCost = floatval($price);
        $taxRate = floatval($tax);
        $cost = $priceToCost / ((1 + $taxRate) / 100);

        // Try stk-replacement-cost first
        if (isset($prodData['stk-replacement-cost']) && $prodData['stk-replacement-cost'] > 0) {
            $cost = $prodData['stk-replacement-cost'];
        }
        // Try stk-current-buy if replacement cost is 0
        elseif (isset($prodData['stk-current-buy']) && $prodData['stk-current-buy'] > 0) {
            $cost = $prodData['stk-current-buy'];
        }
        // Try warehouse average cost
        elseif (isset($prodData['whse-avg-cost-swhs']) && $prodData['whse-avg-cost-swhs'] > 0) {
            $cost = $prodData['whse-avg-cost-swhs'];
        }

        return $cost;
    }

    /**
     * Set product brand during creation
     */
    protected function setProductBrand($product, array $prodData, &$forLogs, $verbose = false)
    {
        if (($prodData['stk-brand-desc'] ?? '') === 'digiSeconds') {
            $brandName = strtolower($prodData['d2brand'] ?? $prodData['stk-brand'] ?? '');
        } else {
            $brandName = strtolower($prodData['stk-brand'] ?? '');
        }

        $brandName = self::BRAND_MAPPINGS[$brandName] ?? $brandName;

        if (isset($this->attributeOptions[$brandName])) {
            $product->setBrand($this->attributeOptions[$brandName]);
            $forLogs .= "Brand: $brandName\n";

            if ($verbose) {
                echo "Brand: $brandName<br/>\n";
            }
        }
    }

    /**
     * Set product barcodes/GTINs
     */
    protected function setProductBarcodes($product, array $prodData, &$forLogs, $verbose = false)
    {
        $barcode1 = $barcode2 = $barcode3 = $barcode4 = "";

        if (isset($prodData['gtins']['gtin'])) {
            // Check if single GTIN or array
            if (count($prodData['gtins']['gtin']) == count($prodData['gtins']['gtin'], COUNT_RECURSIVE)) {
                $barcode1 = $prodData['gtins']['gtin']['id'] ?? '';
            } else {
                $x = 1;
                foreach ($prodData['gtins']['gtin'] as $gtin) {
                    switch ($x) {
                        case 1:
                            $barcode1 = $gtin['id'] ?? '';
                            break;
                        case 2:
                            $barcode2 = $gtin['id'] ?? '';
                            break;
                        case 3:
                            $barcode3 = $gtin['id'] ?? '';
                            break;
                        case 4:
                            $barcode4 = $gtin['id'] ?? '';
                            break;
                    }
                    $x++;
                }
            }
        }

        $product->setCustomAttribute('barcode1', $barcode1);
        $product->setCustomAttribute('barcode2', $barcode2);
        $product->setCustomAttribute('barcode3', $barcode3);
        $product->setCustomAttribute('barcode4', $barcode4);

        $forLogs .= "Barcodes: $barcode1, $barcode2, $barcode3, $barcode4\n";
    }

    /**
     * Set warehouse inventory during product creation
     */
    protected function setWarehouseInventory($product, array $prodData, &$forLogs, $verbose = false)
    {
        if (!isset($prodData['warehouse']['whse'])) {
            return;
        }

        $warehouses = $prodData['warehouse']['whse'];

        // Handle single warehouse vs multiple
        if (!is_array(reset($warehouses))) {
            $warehouses = [$warehouses];
        }

        foreach ($warehouses as $warehouse) {
            if (!is_array($warehouse)) {
                continue;
            }

            $sourceCode = $warehouse['code'] ?? null;
            $quantity = $warehouse['qty_available'] ?? 0;

            if (!$sourceCode) {
                continue;
            }

            try {
                $sourceItem = $this->sourceItemFactory->create();
                $sourceItem->setSourceCode($sourceCode);
                $sourceItem->setSku($prodData['code']);
                $sourceItem->setStatus(1);
                $sourceItem->setQuantity($quantity);

                $this->sourceItemsSaveInterface->execute([$sourceItem]);

                $forLogs .= "$sourceCode: $quantity\n";

                if ($verbose) {
                    echo "$sourceCode: $quantity<br/>\n";
                }
            } catch (\Exception $e) {
                $this->logger->error("Error setting warehouse inventory for " . $sourceCode . ": " . $e->getMessage());
            }
        }
    }

    /**
     * Set digiSeconds specific attributes
     */
    protected function setDigiSecondsAttributes($product, array $prodData)
    {
        $itemCondition = ' ';
        if (isset($prodData['stk-sort-analysis-code']) && !empty($prodData['stk-sort-analysis-code'])) {
            $itemCondition = $prodData['stk-sort-analysis-code'];
        } elseif (isset($prodData['d2lvl1']) && !empty($prodData['d2lvl1'])) {
            $itemCondition = $prodData['d2lvl1'];
        }
        $product->setCustomAttribute('item_condition', $itemCondition);

        $itemRating = $prodData['d2lvl2'] ?? ' ';
        $product->setCustomAttribute('item_rating', $itemRating);

        $d2Desc = $prodData['d2desc'] ?? ' ';
        $product->setCustomAttribute('d2desc', $d2Desc);

        $d2NewSku = $prodData['d2newsku'] ?? ' ';
        $product->setCustomAttribute('d2newsku', $d2NewSku);
    }

    /**
     * Get category tree data
     */
    public function getCategoryData(int $categoryId)
    {
        try {
            return $this->categoryManagement->getTree($categoryId);
        } catch (NoSuchEntityException $e) {
            $this->logger->error("Category not found: " . $categoryId, [$e]);
            return null;
        }
    }
}
