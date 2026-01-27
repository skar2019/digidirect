<?php
namespace Digidirect\Pronto\Helper;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Api\Data\CategoryTreeInterface;
use Magento\Catalog\Api\CategoryManagementInterface;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\CategoryLinkRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku;

class ProductEntHelper extends AbstractHelper
{

    protected $_productCollectionFactory;

    protected $imageHelperFactory;

    protected $_categoryHelper;
    protected $categoryFactory;
    protected $_catalogLayer;
    protected $searchCriteriaBuilder;
    protected $categoryCollectionFactory;
    protected $sourceItemRepository;

    protected $productRepository;
    protected $logger;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        ProductResource $productResource,
        CategoryFactory $categoryFactory,
        CategoryLinkManagementInterface $categoryLinkManagement,
        CategoryLinkRepositoryInterface $categoryLinkRepository,
        CategoryManagementInterface $categoryManagement,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceItemRepositoryInterface $sourceItemRepository,
        CategoryCollectionFactory $categoryCollectionFactory,
        GetSourceItemsBySku $getSourceItemsBySku,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Psr\Log\LoggerInterface $logger  // ADD THIS
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->productResource = $productResource;
        $this->categoryFactory = $categoryFactory;
        $this->categoryLinkManagement = $categoryLinkManagement;
        $this->categoryLinkRepository = $categoryLinkRepository;
        $this->categoryManagement = $categoryManagement;
        $this->_categoryHelper = $categoryHelper;
        $this->stockRegistry = $stockRegistry;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->getSourceItemsBySku = $getSourceItemsBySku;
        $this->productRepository = $productRepository;
        $this->logger = $logger;  // ADD THIS
    }

    public function execute()
    {
        $filepath = 'export/catalog_product_entity.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();
//        $header = ['Id','Sku','Name','AttributeSetId','Price','Status','Visibility','Type',
//            'Weight','Quantity','InStock','QuantityUsesDecimals','ProductImageURL','MinQuantity','UseConfigMinQuantity',
//            'MinSaleQuantity','UseConfigMinSaleQuantity','MaxSaleQuantity',
//            'UseConfigMaxSaleQuantity','Backorders','UseConfigBackorders','NotifyStockQuantity','UseConfigNotifyStockQty',
//            'EnableQtyIncrements','UseConfigEnableQtyIncrements','QuantityIncrements','UseConfigQuantityIncrements',
//            'ManageStock','UseConfigManageStock','ShowDefaultNotificationMessage','LowStockDate','StockStatusChangedAuto','created_at','updated_at'];

        $header = ['Id','Sku','Name','AttributeSetId','Price','Status','Visibility','Type',
            'Weight','Quantity','InStock','QuantityUsesDecimals','MinQuantity','UseConfigMinQuantity',
            'MinSaleQuantity','UseConfigMinSaleQuantity','MaxSaleQuantity',
            'UseConfigMaxSaleQuantity','Backorders','UseConfigBackorders','NotifyStockQuantity','UseConfigNotifyStockQty',
            'EnableQtyIncrements','UseConfigEnableQtyIncrements','QuantityIncrements','UseConfigQuantityIncrements',
            'ManageStock','UseConfigManageStock','ShowDefaultNotificationMessage','LowStockDate','StockStatusChangedAuto','created_at','updated_at'];

        $stream->writeCsv($header);
        $collection = $this->getProductCollection();
        foreach ($collection as $product) {

            $stockItem = $this->stockRegistry->getStockItem($product->getId());
            $isInStock = $stockItem ? $stockItem->getIsInStock() : false;
            if(!$isInStock)
            {
                $isInStock = 0;
            }
            $skus = array($product->getSku());
            $qty = $this->isProductsInStock('SWHS', $skus);
            $data = [];
            $data[] = $product->getId();
            $data[] = $product->getSku();
            $data[] = $product->getName();
            $data[] = $product->getAttributeSetId();
            $data[] = $product->getPrice();
            $data[] = $product->getStatus();
            $data[] = $product->getVisibility();
            $data[] = $product->getTypeId();
            $data[] = $product->getWeight();
            $data[] = $qty;
            $data[] = $isInStock;
            $data[] = $product->getIsQuantityUsesDecimals();
            //$data[] = '';//$this->imageHelperFactory->create()->init($product, 'image')->getUrl();
            $data[] = '1';//$product->getMinQuantity();
            $data[] = $product->getUseConfigMinQuantity();
            $data[] = $product->getMinSaleQuantity();
            $data[] = $product->getUseConfigMinSaleQuantity();
            $data[] = $product->getMaxSaleQuantity();
            $data[] = $product->getUseConfigMaxSaleQuantity();
            $data[] = $product->getBackorders();
            $data[] = $product->getUseConfigBackorders();
            $data[] = $product->getNotifyStockQuantity();
            $data[] = $product->getUseConfigNotifyStockQty();
            $data[] = $product->getEnableQtyIncrements();
            $data[] = $product->getUseConfigEnableQtyIncrements();
            $data[] = $product->getQuantityIncrements();
            $data[] = $product->getUseConfigQuantityIncrements();
            $data[] = $product->getManageStock();
            $data[] = $product->getUseConfigManageStock();
            $data[] = $product->getShowDefaultNotificationMessage();
            $data[] = $product->getLowStockDate();
            $data[] = $product->getStockStatusChangedAuto();
            $data[] = $product->getCreatedAt();
            $data[] = $product->getUpdatedAt();
            $stream->writeCsv($data);
        }
    }

    public function getProductImage()
    {
        $filepath = 'export/mg_productmediagallery.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();

        $header = ['ProductId','ProductURL','ProductImageURL'];

        $stream->writeCsv($header);
        $collection = $this->getProductCollection();
        foreach ($collection as $product) {
            if(empty($product->getImage()))
            {
                continue;
            }

            $imageUrl = $product->getMediaConfig()->getMediaUrl($product->getImage());
            if(empty($imageUrl))
            {
                continue;
            }
            $data = [];
            $data[] = $product->getId();
            $data[] = $product->getProductUrl();
            $data[] = $imageUrl;

            $stream->writeCsv($data);
        }
    }

    public function productData()
    {
        $this->attributeOptions = $this->getOptionHash('brand');

        $parentID = 2; // default category
        $getCategoryList = $this->getSubCategoryByParentID($parentID);

        $filepath = 'export/wiserdata.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();
        $header = ['Brand Name','Description','UPC','SKU','Model Number','Title','Category 1','Category 2',
            'Category 3','Category 4','Price','Cost','Final Price','Stock Condition','Stock Group','Stock On Hand',
            'Stock Division', 'Stock Department','Stock Category','Stock Class','Seller Code','Is PreOrder','Not Eligible for Discount'];

        $stream->writeCsv($header);
        $collection = $this->getProductCollection();

        $errorCount = 0;
        $successCount = 0;
        $noPriceCount = 0;
        $customPriceUsedCount = 0;

        foreach ($collection as $product) {
            $seller = $product->getData('marketplacer_seller');

            if($seller == '20329')
            {
                try {
                    $data = [];

                    // Description handling
                    $description = "";
                    if(!empty($product->getDescription()))
                    {
                        $description = strip_tags($product->getDescription());
                        $description = preg_replace('/[\x00-\x1F\x7F]/u', '', $description);
                    }

                    // Brand handling
                    $brandname = "";
                    if($product->getBrand() && isset($this->attributeOptions[$product->getBrand()]))
                    {
                        $brandname = $this->attributeOptions[$product->getBrand()];
                    }

                    // Category handling
                    $category1 = "";
                    $category2 = "";
                    $category3 = "";
                    $category4 = "";

                    $productCategoryIds = $product->getCategoryIds();
                    if(count($productCategoryIds) > 0)
                    {
                        foreach ($getCategoryList as $id => $category)
                        {
                            if(isset($productCategoryIds[0]) && $category['id'] == $productCategoryIds[0])
                            {
                                $category1 = $category['name'];
                            }

                            if(isset($productCategoryIds[1]) && $category['id'] == $productCategoryIds[1])
                            {
                                $category2 = $category['name'];
                            }

                            if(isset($productCategoryIds[2]) && $category['id'] == $productCategoryIds[2])
                            {
                                $category3 = $category['name'];
                            }

                            if(isset($productCategoryIds[3]) && $category['id'] == $productCategoryIds[3])
                            {
                                $category4 = $category['name'];
                            }
                        }
                    }

                    // GTIN/Barcode handling
                    $gtin = "";
                    $barcode1 = $product->getCustomAttribute('barcode1');
                    if(!is_null($barcode1))
                    {
                        $bc = $barcode1->getValue();
                        if(is_numeric($bc) && $bc > 0)
                        {
                            $gtin = $bc;
                        }
                    }

                    // Fallback to barcode2 if barcode1 is empty or non-numeric
                    if(empty($gtin))
                    {
                        $barcode2 = $product->getCustomAttribute('barcode2');
                        if(!is_null($barcode2))
                        {
                            $bc2 = $barcode2->getValue();
                            if(is_numeric($bc2) && $bc2 > 0)
                            {
                                $gtin = $bc2;
                            }
                        }
                    }

                    // Title handling
                    $title = strip_tags($product->getName());
                    $title = preg_replace('/[\x00-\x1F\x7F]/u', '', $title);

                    // ===== PRICE CALCULATION WITH CATALOG RULES AND CUSTOM_FINAL_PRICE =====

                    // 1. Get Regular Price
                    $regular_price = 0;
                    try {
                        $regular_price = (float) $product->getPrice();

                        // Fallback if getPrice() returns 0
                        if($regular_price == 0)
                        {
                            $priceData = $product->getData('price');
                            $regular_price = $priceData ? (float) $priceData : 0;
                        }
                    } catch (\Exception $e) {
                        $regular_price = 0;
                        $this->logger->warning('Regular price error for SKU ' . $product->getSku() . ': ' . $e->getMessage());
                    }

                    // 2. Get Cost with fallback calculation
                    $actualcost = 0;
                    $cost = $product->getCustomAttribute('cost');
                    if(!is_null($cost))
                    {
                        $actualcost = (float) $cost->getValue();
                    }

                    // Fallback: calculate cost from regular price if cost is 0 or empty
                    if($actualcost == 0 && $regular_price > 0)
                    {
                        $actualcost = $regular_price / 1.1;
                    }

                    // 3. Get Final Price with Catalog Price Rules
                    $final_price = 0;
                    try {
                        // Ensure we're in the correct store context for catalog rules
                        $product->setStoreId(1); // Set your store ID

                        // Method 1: Use PriceInfo (most reliable for catalog rules)
                        $priceInfo = $product->getPriceInfo();
                        if($priceInfo)
                        {
                            $finalPriceModel = $priceInfo->getPrice('final_price');
                            if($finalPriceModel)
                            {
                                $amount = $finalPriceModel->getAmount();
                                if($amount)
                                {
                                    $final_price = (float) $amount->getValue();
                                }
                            }
                        }

                        // Fallback 1: Use data from addFinalPrice()
                        if($final_price == 0)
                        {
                            $final_price = $product->getData('final_price');
                            if(!is_null($final_price))
                            {
                                $final_price = (float) $final_price;
                            }
                        }

                        // Fallback 2: Use getFinalPrice() method
                        if($final_price == 0)
                        {
                            $final_price = (float) $product->getFinalPrice();
                        }

                        // Final fallback: Use regular price
                        if($final_price == 0)
                        {
                            $final_price = $regular_price;
                            if($regular_price > 0)
                            {
                                $this->logger->info('No final price for SKU ' . $product->getSku() . ', using regular price: ' . $regular_price);
                            }
                        }

                    } catch (\Exception $e) {
                        $final_price = $regular_price;
                        $this->logger->warning('Final price error for SKU ' . $product->getSku() . ': ' . $e->getMessage());
                    }

                    // 4. Check custom_final_price and use if lower
                    $customFinalPrice = $product->getCustomAttribute('custom_final_price');
                    if(!is_null($customFinalPrice))
                    {
                        $customFinalPriceValue = (float) $customFinalPrice->getValue();

                        // Use custom_final_price if it's valid and lower than calculated final_price
                        if($customFinalPriceValue > 0 && $customFinalPriceValue < $final_price)
                        {
                            $this->logger->info('Using custom_final_price for SKU ' . $product->getSku() . ' - Catalog Final: ' . $final_price . ', Custom Final: ' . $customFinalPriceValue);
                            $final_price = $customFinalPriceValue;
                            $customPriceUsedCount++;
                        }
                    }

                    // Validation: Log suspicious prices
                    if($final_price > $regular_price * 1.5 && $regular_price > 0)
                    {
                        $this->logger->warning('Final price unusually high for SKU ' . $product->getSku() . ' (Regular: ' . $regular_price . ', Final: ' . $final_price . ')');
                    }

                    // Track products with no price
                    if($final_price == 0 && $regular_price == 0)
                    {
                        $noPriceCount++;
                        $this->logger->error('No price data for SKU ' . $product->getSku());
                        // Optionally skip products with no price
                        // continue;
                    }

                    // Debug logging for price comparison
                    $this->logger->debug('Price details for SKU ' . $product->getSku() . ' - Regular: ' . $regular_price . ', Final: ' . $final_price . ', Custom: ' . ($customFinalPriceValue ?? 'N/A'));

                    // ===== END PRICE CALCULATION =====

                    // Stock Condition
                    $stockC = "Other";
                    $stockcondition = $product->getCustomAttribute('stock_condition');
                    if(!is_null($stockcondition))
                    {
                        $stockConditionValue = $stockcondition->getValue();
                        if ($stockConditionValue == 179) {
                            $stockC = "0";
                        } elseif ($stockConditionValue == 181) {
                            $stockC = "T";
                        } else {
                            $stockC = "Other";
                        }
                    }

                    // Stock Group
                    $sckGrp = "";
                    $stockgroup = $product->getCustomAttribute('stock_group');
                    if(!is_null($stockgroup))
                    {
                        $sckGrp = $stockgroup->getValue();
                    }

                    // Stock on Hand
                    $stockonhand = 0;
                    try {
                        $sourceItems = $this->getSourceItemsBySku->execute($product->getSku());
                        foreach ($sourceItems as $sourceItem) {
                            $stockonhand += $sourceItem->getQuantity();
                        }
                    } catch (\Exception $e) {
                        $stockonhand = 0;
                        $this->logger->warning('Stock retrieval error for SKU ' . $product->getSku() . ': ' . $e->getMessage());
                    }

                    // Stock Division
                    $stockDivision = "";
                    $stockDivisionAttr = $product->getCustomAttribute('stock_division');
                    if(!is_null($stockDivisionAttr))
                    {
                        $stockDivision = $stockDivisionAttr->getValue();
                    }

                    // Stock Department
                    $stockDepartment = "";
                    $stockDepartmentAttr = $product->getCustomAttribute('stock_department');
                    if(!is_null($stockDepartmentAttr))
                    {
                        $stockDepartment = $stockDepartmentAttr->getValue();
                    }

                    // Stock Category
                    $stockCategory = "";
                    $stockCategoryAttr = $product->getCustomAttribute('stock_category');
                    if(!is_null($stockCategoryAttr))
                    {
                        $stockCategory = $stockCategoryAttr->getValue();
                    }

                    // Stock Class
                    $stockClass = "";
                    $stockClassAttr = $product->getCustomAttribute('stock_class');
                    if(!is_null($stockClassAttr))
                    {
                        $stockClass = $stockClassAttr->getValue();
                    }

                    // Pre-order status
                    $isPreOrder = 0;
                    $isPreOrderAttr = $product->getCustomAttribute('pre_order_status');
                    if(!is_null($isPreOrderAttr))
                    {
                        $preOrderValue = $isPreOrderAttr->getValue();
                        if($preOrderValue == 1 || $preOrderValue == 2)
                        {
                            $isPreOrder = 1;
                        }
                    }

                    // Discount eligibility
                    $noteligiblefordiscount = 0;
                    $noteligiblefordiscountAttr = $product->getCustomAttribute('not_eligible_for_discount');
                    if(!is_null($noteligiblefordiscountAttr))
                    {
                        $noteligiblefordiscount = $noteligiblefordiscountAttr->getValue();
                    }

                    // Build CSV row
                    $data[] = $brandname;
                    $data[] = $description;
                    $data[] = $gtin;
                    $data[] = $product->getSku();
                    $data[] = $product->getApn();
                    $data[] = $title;
                    $data[] = $category1;
                    $data[] = $category2;
                    $data[] = $category3;
                    $data[] = $category4;
                    $data[] = number_format($regular_price, 2, '.', ''); // Format price to 2 decimals
                    $data[] = number_format($actualcost, 2, '.', '');
                    $data[] = number_format($final_price, 2, '.', '');
                    $data[] = $stockC;
                    $data[] = $sckGrp;
                    $data[] = $stockonhand;
                    $data[] = $stockDivision;
                    $data[] = $stockDepartment;
                    $data[] = $stockCategory;
                    $data[] = $stockClass;
                    $data[] = $seller;
                    $data[] = $isPreOrder;
                    $data[] = $noteligiblefordiscount;

                    $stream->writeCsv($data);
                    $successCount++;

                } catch (\Exception $e) {
                    $errorCount++;
                    $this->logger->error('Error processing product SKU ' . $product->getSku() . ': ' . $e->getMessage());
                    // Continue to next product instead of failing entire export
                    continue;
                }
            }
        }

        $stream->unlock();
        $stream->close();

        // Log summary
        $this->logger->info("Export completed. Success: {$successCount}, Errors: {$errorCount}, No Price: {$noPriceCount}, Custom Price Used: {$customPriceUsedCount}");

        exit;
    }


    public function categorySalesForce()
    {
        $filepath = 'export/catalog_category_entity.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();
        $header = ['Id','Name','IsActive','Position','Level','ParentId','IncludeInNavigationMenu','AvailableProductListinSortBy',
            'created_at','updated_at'];

        $stream->writeCsv($header);

        $getSubCategory = $this->categoryManagement->getTree(2);
        foreach ($getSubCategory->getChildrenData() as $subcategory) {
            echo $subcategory->getId() ." sub1 <br/>";
            $data = [];
            $data[] = $subcategory->getId();
            $data[] = $subcategory->getName();
            $data[] = $subcategory->getIsActive();
            $data[] = $subcategory->getPosition();
            $data[] = $subcategory->getLevel();
            $data[] = $subcategory->getParentId();
            $data[] = $subcategory->getIncludeInMenu();
            $data[] = $subcategory->getDefaultProductListingSortBy();
            $data[] = $subcategory->getCreatedAt();
            $data[] = $subcategory->getUpdateAt();
            $stream->writeCsv($data);

            //echo count($subcategory->getChildrenData());
            if (count($subcategory->getChildrenData())) {
                $getSubCategoryLevelDown = $this->getCategoryData($subcategory->getId());
                foreach ($getSubCategoryLevelDown->getChildrenData() as $sub1category) {
                    echo $sub1category->getId() ." sub2 <br/>";
                    $data = [];
                    $data[] = $sub1category->getId();
                    $data[] = $sub1category->getName();
                    $data[] = $sub1category->getIsActive();
                    $data[] = $sub1category->getPosition();
                    $data[] = $sub1category->getLevel();
                    $data[] = $sub1category->getParentId();
                    $data[] = $sub1category->getIncludeInMenu();
                    $data[] = $sub1category->getDefaultProductListingSortBy();
                    $data[] = $sub1category->getCreatedAt();
                    $data[] = $sub1category->getUpdateAt();
                    $stream->writeCsv($data);

                    if (count($sub1category->getChildrenData())) {
                        $getSubCategoryLevelDownAgain = $this->getCategoryData($sub1category->getId());
                        foreach ($getSubCategoryLevelDownAgain->getChildrenData() as $sub2category) {
                            $data = [];
                            $data[] = $sub2category->getId();
                            $data[] = $sub2category->getName();
                            $data[] = $sub2category->getIsActive();
                            $data[] = $sub2category->getPosition();
                            $data[] = $sub2category->getLevel();
                            $data[] = $sub2category->getParentId();
                            $data[] = $sub2category->getIncludeInMenu();
                            $data[] = $sub2category->getDefaultProductListingSortBy();
                            $data[] = $sub2category->getCreatedAt();
                            $data[] = $sub2category->getUpdateAt();
                            $stream->writeCsv($data);

                            if (count($sub2category->getChildrenData())) {
                                $getSubCategoryLevelDownAgain4 = $this->getCategoryData($sub2category->getId());
                                foreach ($getSubCategoryLevelDownAgain4->getChildrenData() as $sub3category) {
                                    $data = [];
                                    $data[] = $sub3category->getId();
                                    $data[] = $sub3category->getName();
                                    $data[] = $sub3category->getIsActive();
                                    $data[] = $sub3category->getPosition();
                                    $data[] = $sub3category->getLevel();
                                    $data[] = $sub3category->getParentId();
                                    $data[] = $sub3category->getIncludeInMenu();
                                    $data[] = $sub3category->getDefaultProductListingSortBy();
                                    $data[] = $sub3category->getCreatedAt();
                                    $data[] = $sub3category->getUpdateAt();
                                    $stream->writeCsv($data);
                                }
                            }
                        }
                    }
                }
            }
        }


    }

    public function catalogcategoryproduct()
    {

        $parentID = 2; // default category
        //$getCategoryList = $this->getSubCategoryByParentID($parentID);

        $filepath = 'export/catalog_category_product_6.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();
        $header = ['ProductSku','CategoryId','Position'];

        $stream->writeCsv($header);
        $collection = $this->getProductCollection();
        $id = "";
        foreach ($collection as $product) {
            $data = [];
            $data[] = $product->getSku();
            $categoryId = '';
            $productCategoryIds = $product->getCategoryIds();
            if((count($productCategoryIds)))
            {
                $categoryId = $productCategoryIds[0];


                if(isset($productCategoryIds[1]))
                {
                    $categoryId = $productCategoryIds[1];
                }

                if(isset($productCategoryIds[2]))
                {
                    $categoryId = $productCategoryIds[2];
                }

                if(isset($productCategoryIds[3]))
                {
                    $categoryId = $productCategoryIds[3];
                }

            }
            $data[] = $categoryId;
            $productPosition = "";

            //$position = $this->getProductCategoryPosition($product, $categoryId);
            //$categories = $product->getCategoryIds();

            $categoryCollection = $this->categoryCollectionFactory->create();
            $categoryCollection->addFieldToFilter('entity_id', $categoryId);
            $category = $categoryCollection->getFirstItem();
            $position = $category->getProductsPosition();
            if(!isset($position[$product->getId()]))
            {
                $productPosition = 0;
            }
            else
            {
                $productPosition = $position[$product->getId()];
            }
            if(empty($categoryId))
            {
                $productPosition = "";
            }
            $data[] = $productPosition;
            $stream->writeCsv($data);
        }


    }

    public function getProductCollection()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*')
            ->addStoreFilter(1)
            ->addFinalPrice() // This should apply catalog rules
            ->addFieldToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

        // IMPORTANT: Set the website and customer group for price rules
        $collection->addPriceData(0, 1); // (customerGroupId, websiteId)

        return $collection;
    }

    protected function getOptionHash(string $attributeCode): array
    {
        $result = [];
        $attribute = $this->productResource->getAttribute($attributeCode);
        $options = $attribute->getSource()->getAllOptions(false);
        foreach ($options as $option) {
            if (!isset($option['value']) || !strlen($option['value'])) {
                continue;
            }
            $result[strtolower($option['value'])] = $option['label'];
        }

        return $result;
    }

    public function getSubCategoryByParentID(int $categoryId): array
    {
        $categoryData = [];

        $getSubCategory = $this->getCategoryData($categoryId);
        foreach ($getSubCategory->getChildrenData() as $category) {
            $categoryData[$category->getId()] = [
                'name'=> $category->getName(),
                'url'=> $category->getUrl(),
                'id'=> $category->getId()
            ];
            if (count($category->getChildrenData())) {
                $getSubCategoryLevelDown = $this->getCategoryData($category->getId());
                foreach ($getSubCategoryLevelDown->getChildrenData() as $subcategory) {
                    $categoryData[$subcategory->getId()]  = [
                        'name'=> $subcategory->getName(),
                        'url'=> $subcategory->getUrl(),
                        'id'=> $subcategory->getId()
                    ];
                    if (count($subcategory->getChildrenData())) {
                        $getSubCategoryLevelDownAgain = $this->getCategoryData($subcategory->getId());
                        foreach ($getSubCategoryLevelDownAgain->getChildrenData() as $sub2category) {
                            $categoryData[$sub2category->getId()]  = [
                                'name'=> $sub2category->getName(),
                                'url'=> $sub2category->getUrl(),
                                'id'=> $sub2category->getId()
                            ];
                            if (count($sub2category->getChildrenData())) {
                                $getSubCategoryLevelDownAgain4 = $this->getCategoryData($sub2category->getId());
                                foreach ($getSubCategoryLevelDownAgain4->getChildrenData() as $sub3category) {
                                    $categoryData[$sub3category->getId()]  = [
                                        'name'=> $sub3category->getName(),
                                        'url'=> $sub3category->getUrl(),
                                        'id'=> $sub3category->getId()
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        return $categoryData;
    }

    public function getCategoryData(int $categoryId): ?CategoryTreeInterface
    {
        try {
            $getSubCategory = $this->categoryManagement->getTree($categoryId);
        } catch (NoSuchEntityException $e) {
            $this->logger->error("Category not found", [$e]);
            $getSubCategory = null;
        }

        return $getSubCategory;
    }

    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->_categoryHelper->getStoreCategories($sorted , $asCollection, $toLoad);
    }

    protected function getSourceItemBySourceCodeAndSku($sourceCode, array $sku) {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceItemInterface::SOURCE_CODE, $sourceCode)
            ->addFilter(SourceItemInterface::SKU, $sku, 'in')
            ->create();
        $sourceItemsResult = $this->sourceItemRepository->getList($searchCriteria);
        return $sourceItemsResult->getItems();
    }

    protected function isProductsInStock($sourceCode, array $productsSkus) {
        $sourceItems = $this->getSourceItemBySourceCodeAndSku($sourceCode, $productsSkus);
        $count = 0;
        foreach ($sourceItems as $sourceItem) {
            $count += $sourceItem->getQuantity();
        }
        return $count;
    }

    public function getProductCategoryPosition(Product $product, $categoryId)
    {
        $categories = $product->getCategoryIds();
        if (!in_array($categoryId, $categories)) {
            return false;
        }
        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addFieldToFilter('entity_id', $categoryId);
        $category = $categoryCollection->getFirstItem();
        $position = $category->getProductsPosition();
        return isset($position[$product->getId()]) ? $position[$product->getId()] : false;
    }

    public function customFinalPrice()
    {

        exit;
        $collection = $this->getProductCollection();
        foreach ($collection as $product) {

            $seller = $product->getData('marketplacer_seller');
            if($seller == '20329' || $seller == 20329)
            {

                $finalprice4 = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue(); //tested 10-10-2024

                $product->setCustomAttribute('custom_final_price', $finalprice4);
                $this->productRepository->save($product);

            }

        }

        exit;

    }

    public function testCustomFinalPrice()
    {

        $collection = $this->getProductCollection();
        $counter = 0;
        foreach ($collection as $product) {

            $seller = $product->getData('marketplacer_seller');
            echo $seller;
            echo "<br /> \n";
            if($seller == '20329' || $seller == 20329)
            {
                echo $product->getSku();
                echo "<br /> \n";
                $finalPrice = $product->getFinalPrice();
                echo "final price 0: ".$finalPrice;
                echo "<br /> \n";
                $final_price = $product->getData('final_price');
                echo "final price 1: ".$final_price;
                $finalprice2 = $product->getPriceInfo()->getPrice('final_price')->getValue();
                echo "<br /> \n";
                echo "final price 2: ".$finalprice2;

                $finalprice3 = $product->getFinalPrice();
                echo "<br /> \n";
                echo "final price 3: ".$finalprice3;

                $finalprice4 = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
                echo "<br /> \n";
                echo "final price 4: ".$finalprice4;
//                $product->setCustomAttribute('custom_final_price', $final_price);
//                $this->productRepository->save($product);
                $counter++;
            }

            if($counter > 50)
            {
                exit;
            }

        }
        exit;

    }

    public function mwaveData()
    {

        $this->attributeOptions = $this->getOptionHash('brand');

        $parentID = 2; // default category
        $getCategoryList = $this->getSubCategoryByParentID($parentID);

        $filepath = 'export/digidirect.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();
        $header = ['Brand Name','Description','UPC','SKU','Model Number','Title','Category 1','Category 2',
            'Category 3','Category 4','Price','Average Cost','Cost','Final Price','Stock Condition','Stock Group','3WHS','Stock On Hand',
            'Stock Division', 'Stock Department','Stock Category','Stock Class','Seller Code','Is PreOrder','Not Eligible for Discount'];

        $stream->writeCsv($header);
        $collection = $this->getProductCollection();
        $id = "";
        foreach ($collection as $product) {
            $data = [];
            $description = "";

            $seller = $product->getData('marketplacer_seller');
            if($seller == '20329')
            {
                if(!empty($product->getDescription()))
                {
                    $description = strip_tags($product->getDescription());
                    $description = preg_replace('/[\x00-\x1F\x7F]/u', '', $description);
                }

                if(isset($this->attributeOptions[$product->getBrand()]))
                {
                    $brandname = $this->attributeOptions[$product->getBrand()];

                }
                else
                {
                    $brandname = "";
                }

                $category1 = "";
                $category2 = "";
                $category3 = "";
                $category4 = "";
                //echo $product->getId() ."<br/>";
                $productCategoryIds = $product->getCategoryIds();
                if((count($productCategoryIds)))
                {
                    foreach ($getCategoryList as $id => $category)
                    {

                        if($category['id'] == $productCategoryIds[0])
                        {
                            $category1 =$category['name'];
                        }

                        if(isset($productCategoryIds[1]))
                        {
                            if($category['id'] == $productCategoryIds[1])
                            {
                                $category2 =$category['name'];
                            }
                        }

                        if(isset($productCategoryIds[2]))
                        {
                            if($category['id'] == $productCategoryIds[2])
                            {
                                $category3 =$category['name'];
                            }
                        }

                        if(isset($productCategoryIds[3]))
                        {
                            if($category['id'] == $productCategoryIds[3])
                            {
                                $category4 =$category['name'];
                            }
                        }

                    }
                }

                //echo $product->getBarcode1()."b1 <br/>";
                $gtin = "";
                $barcode1 = $product->getCustomAttribute('barcode1');
                if(is_null($barcode1))
                {
                    $barcode2 = $product->getCustomAttribute('barcode2');
                    if(is_null($barcode2))
                    {

                    }

                }
                else
                {
                    $bc = $barcode1->getValue();
                    if(is_numeric($bc))
                    {
                        $gtin = $bc;
                    }
                    else
                    {
                        $barcode2 = $product->getCustomAttribute('barcode2');//$product->getCustomAttribute('barcode2')->getValue();
                        if(is_null($barcode2))
                        {

                        }
                        else
                        {

                        }

                    }
                }

                $title = $product->getName();
                $title = strip_tags($title);
                $title = preg_replace('/[\x00-\x1F\x7F]/u', '', $title);
                $regular_price = $product->getPriceInfo()->getPrice('regular_price')->getValue();
                $actualcost = 0;
                $cost = $product->getCustomAttribute('cost');
                if(is_null($cost))
                {
                    $actualcost = $regular_price / 1.1;
                }
                else
                {
                    $actualcost = $cost->getValue();
                    if($actualcost == 0)
                    {
                        $actualcost = $regular_price / 1.1;
                    }
                }

                $avgcost = 0;
                $costavg = $product->getCustomAttribute('avg_cost');
                if(is_null($costavg))
                {
                    $avgcost = $regular_price / 1.1;
                }
                else
                {
                    $avgcost = $costavg->getValue();
                    if($avgcost == 0)
                    {
                        $avgcost = $regular_price / 1.1;
                    }
                }

                $avgcost = number_format($avgcost, 2, '.', '');


                $final_price3 = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();

                $stockC = "Other";
                $stockcondition = $product->getCustomAttribute('stock_condition');

                if(is_null($stockcondition))
                {
                    $stockC = "Other";
                }
                else {
                    $stockC = $stockcondition->getValue();

                    if ($stockC == 179) {
                        $stockC = "0";
                    } elseif ($stockC == 181) {
                        $stockC = "T";
                    } else {
                        $stockC = "Other";
                    }
                }

                $sckGrp = "";
                $stockgroup = $product->getCustomAttribute('stock_group');
                if(!is_null($stockgroup))
                {
                    $sckGrp = $stockgroup->getValue();
                }
                //echo $sckGrp."<br/>";

                $sourceItems = $this->getSourceItemsBySku->execute($product->getSku());

                $stockonhand = 0;
                $threewhs = 0;
                foreach ($sourceItems as $sourceItemId => $sourceItem)
                {
                    $code = $sourceItem->getSourceCode();
                    if($code == '3WHS' )
                    {
                        $threewhs = $sourceItem->getQuantity();
                    }
                    else
                    {
                        $stockonhand += $sourceItem->getQuantity();
                    }

                }

                $stockDivision = $product->getCustomAttribute('stock_division');
                if(!is_null($stockDivision))
                {
                    $stockDivision = $stockDivision->getValue();
                }
                $stockDepartment = $product->getCustomAttribute('stock_department');
                if(!is_null($stockDepartment))
                {
                    $stockDepartment = $stockDepartment->getValue();
                }
                $stockCategory = $product->getCustomAttribute('stock_category');
                if(!is_null($stockCategory))
                {
                    $stockCategory = $stockCategory->getValue();
                }
                $stockClass = $product->getCustomAttribute('stock_class');
                if(!is_null($stockClass))
                {
                    $stockClass = $stockClass->getValue();
                }

                $isPreOrder = $product->getCustomAttribute('pre_order_status');
                if(!is_null($isPreOrder))
                {
                    $isPreOrder = $isPreOrder->getValue();
                    if($isPreOrder == 1 || $isPreOrder == 2)
                    {
                        $isPreOrder = 1;
                    }
                }

                $noteligiblefordiscount = $product->getCustomAttribute('not_eligible_for_discount');
                if(!is_null($noteligiblefordiscount))
                {
                    $noteligiblefordiscount = $noteligiblefordiscount->getValue();
                }
                else
                {
                    $noteligiblefordiscount = 0;
                }

                //echo $stockonhand."<br/>";
                $data[] = $brandname;
                $data[] = $description;
                $data[] = $gtin;
                $data[] = $product->getSku();
                $data[] = $product->getApn();
                $data[] = $title;
                $data[] = $category1;
                $data[] = $category2;
                $data[] = $category3;
                $data[] = $category4;
                $data[] = $regular_price;
                $data[] = $avgcost;
                $data[] = $actualcost;
                $data[] = $final_price3;
                $data[] = $stockC;
                $data[] = $sckGrp;
                $data[] = $threewhs;
                $data[] = $stockonhand;
                $data[] = $stockDivision;
                $data[] = $stockDepartment;
                $data[] = $stockCategory;
                $data[] = $stockClass;
                $data[] = $seller;
                $data[] = $isPreOrder;
                $data[] = $noteligiblefordiscount;

                $stream->writeCsv($data);
            }

        }

    }

    public function debugWiserData()
    {

        //$this->attributeOptions = $this->getOptionHash('brand');

        $parentID = 2; // default category
        //$getCategoryList = $this->getSubCategoryByParentID($parentID);

//        $filepath = 'export/debugwiserdata.csv';
//        $this->directory->create('export');
//        $stream = $this->directory->openFile($filepath, 'w+');
//        $stream->lock();
//        $header = ['Brand Name','Description','UPC','SKU','Model Number','Title','Category 1','Category 2',
//            'Category 3','Category 4','Price','Cost','Final Price','Stock Condition','Stock Group','Stock On Hand',
//            'Stock Division', 'Stock Department','Stock Category','Stock Class','Seller Code','Is PreOrder','Not Eligible for Discount'];
//
//        $stream->writeCsv($header);
        $collection = $this->getProductCollection();
        $id = "";
        foreach ($collection as $product) {
            $data = [];
            $description = "";

            $seller = $product->getData('marketplacer_seller');
            if($seller == '20329')
            {
//                if(!empty($product->getDescription()))
//                {
//                    $description = strip_tags($product->getDescription());
//                    $description = preg_replace('/[\x00-\x1F\x7F]/u', '', $description);
//                }
//
//                if(isset($this->attributeOptions[$product->getBrand()]))
//                {
//                    $brandname = $this->attributeOptions[$product->getBrand()];
//
//                }
//                else
//                {
//                    $brandname = "";
//                }
//
//                $category1 = "";
//                $category2 = "";
//                $category3 = "";
//                $category4 = "";
//                //echo $product->getId() ."<br/>";
//                $productCategoryIds = $product->getCategoryIds();
//                if((count($productCategoryIds)))
//                {
//                    foreach ($getCategoryList as $id => $category)
//                    {
//
//                        if($category['id'] == $productCategoryIds[0])
//                        {
//                            $category1 =$category['name'];
//                        }
//
//                        if(isset($productCategoryIds[1]))
//                        {
//                            if($category['id'] == $productCategoryIds[1])
//                            {
//                                $category2 =$category['name'];
//                            }
//                        }
//
//                        if(isset($productCategoryIds[2]))
//                        {
//                            if($category['id'] == $productCategoryIds[2])
//                            {
//                                $category3 =$category['name'];
//                            }
//                        }
//
//                        if(isset($productCategoryIds[3]))
//                        {
//                            if($category['id'] == $productCategoryIds[3])
//                            {
//                                $category4 =$category['name'];
//                            }
//                        }
//
//                    }
//                }
//
//                //echo $product->getBarcode1()."b1 <br/>";
//                $gtin = "";
//                $barcode1 = $product->getCustomAttribute('barcode1');
//                if(is_null($barcode1))
//                {
//                    $barcode2 = $product->getCustomAttribute('barcode2');
//                    if(is_null($barcode2))
//                    {
//
//                    }
//
//                }
//                else
//                {
//                    $bc = $barcode1->getValue();
//                    if(is_numeric($bc))
//                    {
//                        $gtin = $bc;
//                    }
//                    else
//                    {
//                        $barcode2 = $product->getCustomAttribute('barcode2');//$product->getCustomAttribute('barcode2')->getValue();
//                        if(is_null($barcode2))
//                        {
//
//                        }
//                        else
//                        {
//
//                        }
//
//                    }
//                }
//
//                $title = $product->getName();
//                $title = strip_tags($title);
//                $title = preg_replace('/[\x00-\x1F\x7F]/u', '', $title);
//                $regular_price = $product->getPriceInfo()->getPrice('regular_price')->getValue();
//                $actualcost = 0;
//                $cost = $product->getCustomAttribute('cost');
//                if(is_null($cost))
//                {
//                    $actualcost = $regular_price / 1.1;
//                }
//                else
//                {
//                    $actualcost = $cost->getValue();
//                    if($actualcost == 0)
//                    {
//                        $actualcost = $regular_price / 1.1;
//                    }
//                }
//
//                $final_price = $product->getPriceInfo()->getPrice('final_price')->getValue();
//                $final_price2 = $product->getFinalPrice();
//                $final_price3 = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
////                $product->setCustomAttribute('custom_final_price', $final_price3);
////                $this->productRepository->save($product);
////            echo "final price ".$final_price."<br/>";
////            echo "final price2 ".$final_price2."<br/>";
////            echo $product->getSku()." final price3 ".$final_price3."<br/>";
//                $stockC = "Other";
//                $stockcondition = $product->getCustomAttribute('stock_condition');
//
//                if(is_null($stockcondition))
//                {
//                    $stockC = "Other";
//                }
//                else {
//                    $stockC = $stockcondition->getValue();
//
//                    if ($stockC == 179) {
//                        $stockC = "0";
//                    } elseif ($stockC == 181) {
//                        $stockC = "T";
//                    } else {
//                        $stockC = "Other";
//                    }
//                }
//
//                $sckGrp = "";
//                $stockgroup = $product->getCustomAttribute('stock_group');
//                if(!is_null($stockgroup))
//                {
//                    $sckGrp = $stockgroup->getValue();
//                }
//                //echo $sckGrp."<br/>";
//
//                $sourceItems = $this->getSourceItemsBySku->execute($product->getSku());
//
//                $stockonhand = 0;
//                foreach ($sourceItems as $sourceItemId => $sourceItem) {
//
//                    $stockonhand += $sourceItem->getQuantity();
//                }
//
//                $stockDivision = $product->getCustomAttribute('stock_division');
//                if(!is_null($stockDivision))
//                {
//                    $stockDivision = $stockDivision->getValue();
//                }
//                $stockDepartment = $product->getCustomAttribute('stock_department');
//                if(!is_null($stockDepartment))
//                {
//                    $stockDepartment = $stockDepartment->getValue();
//                }
//                $stockCategory = $product->getCustomAttribute('stock_category');
//                if(!is_null($stockCategory))
//                {
//                    $stockCategory = $stockCategory->getValue();
//                }
//                $stockClass = $product->getCustomAttribute('stock_class');
//                if(!is_null($stockClass))
//                {
//                    $stockClass = $stockClass->getValue();
//                }
//
//                $isPreOrder = $product->getCustomAttribute('pre_order_status');
//                if(!is_null($isPreOrder))
//                {
//                    $isPreOrder = $isPreOrder->getValue();
//                    if($isPreOrder == 1 || $isPreOrder == 2)
//                    {
//                        $isPreOrder = 1;
//                    }
//                }
//
//                $noteligiblefordiscount = $product->getCustomAttribute('not_eligible_for_discount');
//                if(!is_null($noteligiblefordiscount))
//                {
//                    $noteligiblefordiscount = $noteligiblefordiscount->getValue();
//                }
//                else
//                {
//                    $noteligiblefordiscount = 0;
//                }

                echo $product->getSku() ."<br/>";

//                $data[] = $brandname;
//                $data[] = $description;
//                $data[] = $gtin;
//                $data[] = $product->getSku();
//                $data[] = $product->getApn();
//                $data[] = $title;
//                $data[] = $category1;
//                $data[] = $category2;
//                $data[] = $category3;
//                $data[] = $category4;
//                $data[] = $regular_price;
//                $data[] = $actualcost;
//                $data[] = $final_price3;
//                $data[] = $stockC;
//                $data[] = $sckGrp;
//                $data[] = $stockonhand;
//                $data[] = $stockDivision;
//                $data[] = $stockDepartment;
//                $data[] = $stockCategory;
//                $data[] = $stockClass;
//                $data[] = $seller;
//                $data[] = $isPreOrder;
//                $data[] = $noteligiblefordiscount;
//
//                $stream->writeCsv($data);
            }

        }

        exit;

    }

    public function mwaveExtraData()
    {

        $this->attributeOptions = $this->getOptionHash('brand');

        $parentID = 2; // default category
        $getCategoryList = $this->getSubCategoryByParentID($parentID);

        $filepath = 'export/digiDirectProductAttributes.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();
        $header = ['SKU','Product Title','Brand Name','Model Number','UPC','Description','Category 1','Category 2',
            'Category 3','Category 4','Product Overview','Specs','Whats in the box','Package Weight','Length Width Height',
            'Warranty','Image URLs','Cost Price','Final Price'];

        $stream->writeCsv($header);
        $collection = $this->getProductCollection();
        $id = "";
        foreach ($collection as $product) {
            $data = [];
            $description = "";
            $overview = "";
            $imageurl = "";
            $specification = "";
            $whatsinthebox = "";

            $seller = $product->getData('marketplacer_seller');
            if($seller == '20329')
            {
                if(!empty($product->getDescription()))
                {
                    $description = $product->getDescription();
                    //$description = preg_replace('/[\x00-\x1F\x7F]/u', '', $description);
                }

                $overview = $product->getShortDescription();

                $specification = $product->getSpecification();

                $whatsinthebox = $product->getWhatsInTheBox();

                if(isset($this->attributeOptions[$product->getBrand()]))
                {
                    $brandname = $this->attributeOptions[$product->getBrand()];

                }
                else
                {
                    $brandname = "";
                }

                $category1 = "";
                $category2 = "";
                $category3 = "";
                $category4 = "";
                //echo $product->getId() ."<br/>";
                $productCategoryIds = $product->getCategoryIds();
                if((count($productCategoryIds)))
                {
                    foreach ($getCategoryList as $id => $category)
                    {

                        if($category['id'] == $productCategoryIds[0])
                        {
                            $category1 =$category['name'];
                        }

                        if(isset($productCategoryIds[1]))
                        {
                            if($category['id'] == $productCategoryIds[1])
                            {
                                $category2 =$category['name'];
                            }
                        }

                        if(isset($productCategoryIds[2]))
                        {
                            if($category['id'] == $productCategoryIds[2])
                            {
                                $category3 =$category['name'];
                            }
                        }

                        if(isset($productCategoryIds[3]))
                        {
                            if($category['id'] == $productCategoryIds[3])
                            {
                                $category4 =$category['name'];
                            }
                        }

                    }
                }

                //echo $product->getBarcode1()."b1 <br/>";
                $gtin = "";
                $barcode1 = $product->getCustomAttribute('barcode1');
                if(is_null($barcode1))
                {
                    $barcode2 = $product->getCustomAttribute('barcode2');
                    if(is_null($barcode2))
                    {

                    }

                }
                else
                {
                    $bc = $barcode1->getValue();
                    if(is_numeric($bc))
                    {
                        $gtin = $bc;
                    }
                    else
                    {
                        $barcode2 = $product->getCustomAttribute('barcode2');//$product->getCustomAttribute('barcode2')->getValue();
                        if(is_null($barcode2))
                        {

                        }
                        else
                        {
                            $gtin = $barcode2->getValue();
                        }

                    }
                }

                $title = $product->getName();
                $title = strip_tags($title);
                $title = preg_replace('/[\x00-\x1F\x7F]/u', '', $title);

                $weight = $product->getWeight();
                $dimensions = "";
                $dimensionsAttr = $product->getCustomAttribute('product_dimensions');
                if(!is_null($dimensionsAttr))
                {
                    $dimensions = $dimensionsAttr->getValue();
                }
                $warranty = "";

                $imageUrl = "";
                if(empty($product->getImage()))
                {
                    $imageUrl = "";
                }

                $imageUrl = $product->getMediaConfig()->getMediaUrl($product->getImage());
                if(empty($imageUrl))
                {
                    $imageUrl = "";
                }

                $regular_price = $product->getPriceInfo()->getPrice('regular_price')->getValue();
                $avgcost = 0;
                $costavg = $product->getCustomAttribute('avg_cost');
                if(is_null($costavg))
                {
                    $avgcost = $regular_price / 1.1;
                }
                else
                {
                    $avgcost = $costavg->getValue();
                    if($avgcost == 0)
                    {
                        $avgcost = $regular_price / 1.1;
                    }
                }

                $avgcost = number_format($avgcost, 2, '.', '');

                $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();

                //echo $stockonhand."<br/>";
                $data[] = $product->getSku();
                $data[] = $title;
                $data[] = $brandname;
                $data[] = $product->getApn();
                $data[] = $gtin;
                $data[] = $description;
                $data[] = $category1;
                $data[] = $category2;
                $data[] = $category3;
                $data[] = $category4;
                $data[] = $overview;
                $data[] = $specification;
                $data[] = $whatsinthebox;
                $data[] = $weight;
                $data[] = $dimensions;
                $data[] = $warranty;
                $data[] = $imageUrl;
                $data[] = $avgcost;
                $data[] = $finalPrice;

                $stream->writeCsv($data);
            }

        }
        exit;
    }

}
