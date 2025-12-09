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
        $this->initializeSync();

        $this->logger->info('Pronto Product Sync - start item: ' . $startItem);

        $json = $this->fetchProntoData($startItem);

        if (!isset($json['stockmaster']['stockcode'])) {
            $this->logger->error('Invalid response structure from Pronto API');
            return false;
        }

        $lastCode = $this->processProducts($json['stockmaster']['stockcode']);

        if (isset($json['response']['status']) && $json['response']['status'] == 'FAIL') {
            $this->logger->info($json['response']['message']);
        }

        $this->productSyncContinue($lastCode);
    }

    /**
     * Continue sync from a specific item
     */
    public function productSyncContinue($startItem)
    {
        if (empty($startItem)) {
            return true;
        }

        $this->initializeSync();
        $this->logger->info('Pronto Product Sync - continue from: ' . $startItem);

        $json = $this->fetchProntoData($startItem);

        if (!isset($json['stockmaster']['stockcode'])) {
            return true;
        }

        $lastCode = $this->processProducts($json['stockmaster']['stockcode']);

        if (isset($json['response']['status']) && $json['response']['status'] == 'FAIL') {
            $this->logger->info($json['response']['message']);
        }

        // Check if we've reached the end
        if ($startItem == $lastCode) {
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

        $json = $this->fetchProntoData($sku, $sku);

        if (!isset($json['stockmaster'])) {
            $this->logger->error('Product not found: ' . $sku);
            return false;
        }

        $this->processProducts([$json['stockmaster']], true);

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

        foreach ($products as $prod) {
            if (!isset($prod['code'])) {
                continue;
            }

            $lastCode = $prod['code'];

            try {
                $this->updateProduct($prod, $verbose);
            } catch (NoSuchEntityException $e) {
                if ($verbose) {
                    $this->logger->info('Product not found, skipping: ' . $prod['code']);
                }
                continue;
            } catch (\Exception $e) {
                $this->logger->error('Error processing product ' . $prod['code'] . ': ' . $e->getMessage());
            }
        }

        return $lastCode;
    }

    /**
     * Update existing product
     */
    protected function updateProduct(array $prodData, $verbose = false)
    {
        $forLogs = "SKU " . $prodData['code'] . "\n";

        $product = $this->productRepository->get($prodData['code']);

        // Update basic product data
        $price = $this->updateProductPricing($product, $prodData, $forLogs);
        $this->updateProductStatus($product, $prodData, $forLogs);
        $this->updateProductBrand($product, $prodData, $forLogs);
        $this->updateProductAttributes($product, $prodData, $forLogs);

        // Calculate and set bestseller metric
        $productSales = $this->getProductSales($product->getId(), $price);
        $product->setCustomAttribute('nb_sales', $productSales);

        // Set update date
        $product->setCustomAttribute('date_update', date('Y-m-d'));

        // Save product first (Magento requirement before category assignment)
        $this->productRepository->save($product);

        // Update categories
        $this->updateProductCategories($product, $prodData, $forLogs);

        $this->logger->info($forLogs);
    }

    /**
     * Update product pricing
     */
    protected function updateProductPricing($product, array $prodData, &$forLogs)
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

        return $price;
    }

    /**
     * Update product status based on conditions
     */
    protected function updateProductStatus($product, array $prodData, &$forLogs)
    {
        $statusFlag = $prodData['stk-user-only-alpha4-1'] ?? '';
        $conditionCode = $prodData['stk-condition-code'] ?? '';

        // Obsolete products are always disabled
        if ($conditionCode === 'O') {
            $product->setStatus(Status::STATUS_DISABLED);
            $forLogs .= "Status: Disabled (Obsolete)\n";
            return;
        }

        // Check web flag
        if (empty($statusFlag) || $statusFlag === 'N') {
            $product->setStatus(Status::STATUS_DISABLED);
            $forLogs .= "Status: Disabled (No web flag)\n";
        } elseif ($statusFlag === 'W') {
            // Web enabled, but check NDA status
            if ($product->getIsNda()) {
                $product->setStatus(Status::STATUS_DISABLED);
                $forLogs .= "Status: Disabled (NDA)\n";
            } else {
                $product->setStatus(Status::STATUS_ENABLED);
                $forLogs .= "Status: Enabled\n";
            }
        }

        // Set pre-order status
        if ($statusFlag === 'P') {
            $product->setCustomAttribute('pre_order_status', '1');
            $forLogs .= "Pre-order: Yes\n";
        }

        // Set awaiting product status
        if ($statusFlag === 'A') {
            $product->setCustomAttribute('awaiting_product', '1');
            $forLogs .= "Awaiting Product: Yes\n";
        } else {
            $product->setCustomAttribute('awaiting_product', '0');
        }
    }

    /**
     * Update product brand
     */
    protected function updateProductBrand($product, array $prodData, &$forLogs)
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
        }
    }

    /**
     * Update various product attributes
     */
    protected function updateProductAttributes($product, array $prodData, &$forLogs)
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
    protected function updateProductCategories($product, array $prodData, &$forLogs)
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

        // Assign categories
        if (count($categoryIds)) {
            try {
                $this->categoryLinkManagement->assignProductToCategories($prodData['code'], $categoryIds);
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
