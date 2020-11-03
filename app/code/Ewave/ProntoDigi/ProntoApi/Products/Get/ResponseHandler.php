<?php

namespace Ewave\ProntoDigi\ProntoApi\Products\Get;

use Ewave\AI\Model\Lib\Entity\Import\Product\Product as ProductImport;
use Ewave\AI\Model\Lib\Entity\Import\Product\ProductFactory as ProductImportFactory;
use Ewave\AI\Model\Lib\Mapping\MapperInterface;
use Ewave\AI\Model\Lib\Validator\Validate;
use Ewave\ProntoDigi\Helper\Config;
use Ewave\ProntoDigi\Helper\Inventory;
use Ewave\ProntoDigi\Model\Import\Sources\SourceItems as SourceItemsImport;
use Ewave\ProntoDigi\Model\ResourceModel\Product as ProductResource;
use Ewave\ProntoDigi\ProntoApi\Constants\Products as ProductConstants;
use Ewave\ProntoDigi\ProntoApi\ProductResponseHandlerAbstract;
use Ewave\ProntoDigi\ProntoApi\Products\Get\Response\CategoryProcessor;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Url;
use Magento\Catalog\Model\ResourceModel\Attribute as AttributeResource;
use Magento\CatalogImportExport\Model\Export\Product as DefaultProductExport;
use Magento\CatalogImportExport\Model\Import\Product;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Exception\LocalizedException;
use Magento\Inventory\Model\SourceItemFactory;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ResponseHandler extends ProductResponseHandlerAbstract
{
    const BRAND_ATTRIBUTE_CODE = 'brand';
    const USE_CONFIG_BACKORDERS = 'use_config_backorders';
    const BACKORDERS = 'allow_backorders';
    const COL_ATTR_SET = 'attribute_set_code';

    /**
     * @var array
     */
    protected $productsToImport = [];

    /**
     * @var AttributeResource
     */
    protected $attributeResource;

    /**
     * @var ProductImportFactory
     */
    protected $productImportFactory;

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * @var null|string
     */
    protected $defaultAttributeSetName;

    /**
     * @var array
     */
    protected $urlKeyToSku;

    /**
     * @var array
     */
    protected $attributeOptions = [];

    /**
     * @var string
     */
    protected $rootCategoryName;

    /**
     * @var array
     */
    protected $failedCategories = [];

    /**
     * @var string
     */
    protected $Product;
    /**
     * ResponseHandler constructor.
     * @param EavConfig $eavConfig
     * @param AttributeResource $attributeResource
     * @param ProductImportFactory $productImportFactory
     * @param Validate $validator
     * @param Url $productUrl
     * @param ProductResource $productResource
     * @param CategoryFactory $categoryFactory
     * @param CategoryProcessor $categoryProcessor
     * @param Inventory $inventoryHelper
     * @param IsSourceItemManagementAllowedForProductTypeInterface $allowedForProductType
     * @param SourceItemsImport $sourceItemsImport
     * @param SourceItemFactory $sourceItemFactory
     * @param Config $configHelper
     * @param MapperInterface|null $mapper
     */
    public function __construct(
        EavConfig $eavConfig,
        AttributeResource $attributeResource,
        ProductImportFactory $productImportFactory,
        Validate $validator,
        Url $productUrl,
        ProductResource $productResource,
        CategoryFactory $categoryFactory,
        CategoryProcessor $categoryProcessor,
        Inventory $inventoryHelper,
        IsSourceItemManagementAllowedForProductTypeInterface $allowedForProductType,
        SourceItemsImport $sourceItemsImport,
        SourceItemFactory $sourceItemFactory,
        Config $configHelper,
        MapperInterface $mapper = null,
        ProductModel $Product
    ) {
        parent::__construct(
            $validator,
            $productUrl,
            $productResource,
            $categoryFactory,
            $categoryProcessor,
            $inventoryHelper,
            $allowedForProductType,
            $sourceItemsImport,
            $sourceItemFactory,
            $configHelper,
            $mapper
        );
        $this->productImportFactory = $productImportFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeResource = $attributeResource;
        $this->Product = $Product;
    }

    /**
     * @param array $response
     * @return array|ProductResponseHandlerAbstract
     */
    public function handle(array $response)
    {
        if ($this->urlKeyToSku === null) {
            $this->urlKeyToSku = $this->productResource->getUrlKeyToSku();
            $this->attributeOptions = [
                self::BRAND_ATTRIBUTE_CODE => $this->getOptionHash(self::BRAND_ATTRIBUTE_CODE),
            ];
        }
        if ($this->rootCategoryName === null) {
            $this->categoryProcessor->initCategories();
            $existsCategories = $this->categoryProcessor->getCategories();
            reset($existsCategories);
            $this->rootCategoryName = key($existsCategories);
            unset($existsCategories);
        }

        $this->productsToImport = array_merge($this->productsToImport, $this->prepareProductsData($response));
        return $this;
    }

    /**
     * @return $this|ProductResponseHandlerAbstract
     * @throws LocalizedException
     */
    public function finalize()
    {
        if ($this->checkDisabledPercent()) {
            $this->productsToImport = $this->newProducts;
            $this->existSkus = [];
        }
        $this->attributeOptions = [];
        $this->urlKeyToSku = null;
        $this->unsetData();
        $this->import($this->productsToImport);
        return $this;
    }

    /**
     * @param string $sku
     * @param string $name
     * @return string
     */
    protected function getUrlKey($sku, $name)
    {
        $urlKey = $this->productUrl->formatUrlKey($name);
        if (isset($this->urlKeyToSku[$urlKey])) {
            if (strtolower($this->urlKeyToSku[$urlKey]) != strtolower($sku)) {
                $urlKey = $urlKey . '-' . $sku;
            }
        }
        $this->urlKeyToSku[$urlKey] = $sku;
        return $urlKey;
    }

    /**
     * @param string $attributeCode
     * @return array
     * @throws LocalizedException
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
     * @param array $products
     * @return $this
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    protected function import(array $products)
    {
        if (empty($products)) {
            $this->logger->info(__('There are no valid items to import.'));
            return $this;
        }
        
        $start = microtime(true);
        /** @var $productImport ProductImport */
        $productImport = $this->productImportFactory->create(['logger' => $this->logger]);
        // set updateOnDuplicate 'false' in order to set 'append' import behavior
        $productImport->saveBunch($products, false);
        $this->saveSourceItems(); 
        $attribute = $this->eavConfig->getAttribute(ProductModel::ENTITY, self::BRAND_ATTRIBUTE_CODE);
        //foreach
        foreach ($products as $key => $product) {
            $sku = $product['sku'];
            $qff_base = $product['additional_attributes']['qff_base'];
            $qff_bonus = $product['additional_attributes']['qff_bonus_points'];
            $attributes = [$qff_base, $qff_bonus];
            //$product = $this->Product->getSku($sku);
            $product_model = $this->Product->loadByAttribute('sku', $sku);
            if ($product_model->offsetExists('qff_base') && $product_model->offsetExists('qff_bonus_points')) {
                $product_model->setCustomAttribute('qff_base', $qff_base);
                $product_model->setCustomAttribute('qff_bonus_points', $qff_bonus);
            }
        }

        if ($attribute->getEntityId()) {
            $this->attributeResource->save($attribute);
        }
        if (!empty($this->existSkus)) {
            foreach ($this->existSkus as &$item) {
                $item = [ProductAttributeInterface::CODE_STATUS => ProductStatus::STATUS_DISABLED];
            }
            $this->productResource->updateProductAttributes($this->existSkus, [ProductAttributeInterface::CODE_STATUS]);
            $this->logger->info(
                __('Disable products which exists in Magento but missing in the interface'),
                array_keys($this->existSkus),
                \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
            );
            $this->existSkus = [];
        }
        $this->logger->info('TIME LOG: Import time: ' . round(microtime(true) - $start, 3) . ' sec.');
        return $this;
    }

    /**
     * @param array $response
     * @return array
     */
    protected function prepareProductsData(array $response)
    {
        $newProducts = [];
        $existsProducts = [];
        foreach ($response as $k => $productData) {
            if ($this->validator->isValid($productData)) {
                try {
                    $productData = $this->mapData($productData);
                    $sku = $productData[ProductInterface::SKU];
                    $name = $productData[ProductInterface::NAME];
                    
                    $isAllowedMultiSource = $this->isAllowedMultiSourceInventoryForProductType(
                        $productData['product_type']
                    );
                    if ($isAllowedMultiSource) {
                        $productData = $this->getInventorySources($productData);
                    } else {
                        unset($productData['sources']);
                    }
                    $productData = $this->getStatus($productData);
                    $productData = $this->getBackorders($productData);
                    $productData = $this->getCategories($productData);
                    $productData = $this->getBrand($productData);
                    $productData = $this->getDescription($productData);
                    unset($productData['sources']);
                    if (!isset($this->getExistSkus()[$sku]) && empty($this->getExcludedSkus()[$sku])) {
                        $productData[ProductAttributeInterface::CODE_STATUS] = ProductStatus::STATUS_DISABLED;
                        if ($isAllowedMultiSource) {
                            $this->assignProductToExistedSources($productData);
                        }

                        $productData[Product::URL_KEY] = $this->getUrlKey($sku, $name);
                        $productData = $this->getAttributeSetForNewProduct($productData);
                        $productData = $this->prepareAdditionalAttributes($productData);
                        $newProducts[$sku] = $productData;
                    } elseif ($this->isProductUpdatePossible($productData)) {
                        $this->filterSourceItemsBeforeUpdate($sku);
                        $productData = $this->prepareExistProduct($productData);
                        $productData = $this->prepareAdditionalAttributes($productData);
                        $existsProducts[$sku] = $productData;
                    }
                } catch (LocalizedException $e) {
                    $this->logInvalidProductInfo($e->getMessage(), $productData);
                }
            } else {
                $this->logInvalidProductInfo(implode('. ', $this->validator->getMessages()), $productData);
            }
            unset($response[$k]);
        }
        $products = array_merge($newProducts, $existsProducts);
        $this->newProducts = array_merge($this->newProducts, $newProducts);
        return $products;
    }

    /**
     * @param array $productData
     * @return bool
     */
    protected function isProductUpdatePossible(array $productData)
    {
         $sku = $productData[ProductInterface::SKU];
        if (!empty($this->getExcludedSkus()[$sku])) {
            $this->logger->info(
                __(
                    'Skipped update: product "%1" is excluded from the integration',
                    $productData[ProductInterface::SKU]
                ),
                $productData,
                \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
            );
            return false;
        }
        if ($this->getExistSkus()[$sku][ProductInterface::TYPE_ID] != $productData['product_type']) {
            $this->logger->warning(
                __(
                    'Product "%1". Product type could not be changed from "%2" to "%3"',
                    $sku,
                    $this->getExistSkus()[$sku][ProductInterface::TYPE_ID],
                    $productData['product_type']
                ),
                $productData,
                \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
            );
            return false;
        }

        return true;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getCategories(array $data)
    {
        $allCategories = [];
        $categoryAttributes = ['category1', 'category2', 'category3', 'category4'];
        $fullCategoryPath = $this->rootCategoryName;
        foreach ($categoryAttributes as $catagoryAttribute) {
            $categoryName = $data[$catagoryAttribute] ?? null;
            $categoryName = trim($categoryName);
            if (empty($categoryName)) {
                break;
            }

            $fullCategoryPath .= CategoryProcessor::DELIMITER_CATEGORY . $categoryName;
            $allCategories[] = $fullCategoryPath;
        }

        $fullPathCategories = implode(',', $allCategories);
        $fullPathCategories = !empty($fullPathCategories) ? $fullPathCategories : $fullCategoryPath;
        $lowerFullCategoryPath = strtolower($fullPathCategories);
        try {
            if (isset($this->failedCategories[$lowerFullCategoryPath])) {
                throw new \Exception($this->failedCategories[$lowerFullCategoryPath]);
            }
            $this->categoryProcessor->upsertCategory($fullCategoryPath);
            $data[Product::COL_CATEGORY] = $fullPathCategories;
        } catch (\Throwable $e) {
            $this->failedCategories[$lowerFullCategoryPath] = (string)$e->getMessage();
            $this->logger->error(
                __(
                    'SKU: "%1". Could not create category "%2": %3',
                    $data[ProductInterface::SKU],
                    $fullCategoryPath,
                    $e->getMessage()
                )
            );
        }

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getBrand(array $data): array
    {
        if (isset($data[self::BRAND_ATTRIBUTE_CODE])) {
            $brand = $data[self::BRAND_ATTRIBUTE_CODE];
            if (!isset($this->attributeOptions[self::BRAND_ATTRIBUTE_CODE][strtolower($brand)])) {
                $this->logger->warning(
                    __(
                        'SKU: "%1". Brand "%2" does not exist',
                        $data[ProductInterface::SKU],
                        $brand
                    )
                );
                unset($data[self::BRAND_ATTRIBUTE_CODE]);
            }
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getDescription(array $data): array
    {
        if (array_key_exists(ProductAttributeInterface::CODE_DESCRIPTION, $data)) {
            $description = (string)$data[ProductAttributeInterface::CODE_DESCRIPTION];
            if (isset($this->getExistSkus()[$data[ProductInterface::SKU]])) {
                unset($data[ProductAttributeInterface::CODE_DESCRIPTION]);
            } elseif (!strlen($description)) {
                unset($data[ProductAttributeInterface::CODE_DESCRIPTION]);
            } elseif ($description == ProductConstants::DESCRIPTION_BEGINNING) {
                $this->logger->warning(
                    __('SKU "%1". uom is not "EACH". conv is empty', $data[ProductInterface::SKU]),
                    [],
                    \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
                );
            }
        }
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getAttributeSetForNewProduct(array $data)
    {
        $standardizedKey = $this->standardizeKey($data[self::COL_ATTR_SET]);
        if (!isset($this->getAttributeSetList()[$standardizedKey])) {
            $attributeSet = $this->getDefaultAttributeSetName();
            $this->logger->warning(
                __(
                    'Attribute Set "%1" does not exist in Magento. Default attribute set is assigned for sku: %2',
                    $data[self::COL_ATTR_SET],
                    $data[ProductInterface::SKU]
                ),
                [],
                \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
            );
        } else {
            $attributeSet = $this->getAttributeSetList()[$standardizedKey];
        }
        $data[self::COL_ATTR_SET] = $attributeSet;
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getAttributeSetForExistProduct(array $data)
    {
        $standardizedKey = $this->standardizeKey($data[self::COL_ATTR_SET]);
        if (!isset($this->getAttributeSetList()[$standardizedKey])) {
            $sku = $data[ProductInterface::SKU];
            $attributeSet = $this->getExistSkus()[$sku]['attribute_set_name'];
            $this->logger->warning(
                __(
                    'Attribute set "%1" does not exist in Magento. 
                     Previous attribute set "%2" remains which was assigned before Integration run for sku: %3',
                    $data[self::COL_ATTR_SET],
                    $attributeSet,
                    $sku
                ),
                [],
                \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
            );
        } else {
            $attributeSet = $this->getAttributeSetList()[$standardizedKey];
        }
        $data[self::COL_ATTR_SET] = $attributeSet;
        return $data;
    }

    /**
     * @param array $productData
     * @return array
     */
    protected function prepareAdditionalAttributes(array $productData): array
    {
        $unsetKeys = [
            'category1',
            'category2',
            'category3',
            'category4',
        ];

        $additionalAttributeCodes = [
            ProductConstants::PRODUCT_ATTRIBUTE_UOM,
            ProductConstants::PRODUCT_ATTRIBUTE_CONVERSION,
            ProductConstants::PRODUCT_ATTRIBUTE_BARCODE1,
            ProductConstants::PRODUCT_ATTRIBUTE_BARCODE2,
            ProductConstants::PRODUCT_ATTRIBUTE_BARCODE3,
            ProductConstants::PRODUCT_ATTRIBUTE_BARCODE4,
            ProductConstants::PRODUCT_ATTRIBUTE_STOCK_STATUS,
            ProductConstants::PRODUCT_ATTRIBUTE_STOCK_CONDITION,
            ProductConstants::PRODUCT_ATTRIBUTE_STOCK_GROUP,
            ProductConstants::PRODUCT_ATTRIBUTE_STOCK_BRAND,
            ProductConstants::PRODUCT_APN,
            ProductConstants::QFF_BASE_POINTS_PER_DOLLAR,
            ProductConstants::QFF_BONUS_POINTS_PER_DOLLAR,
        ];

        foreach ($unsetKeys as $key) {
            unset($productData[$key]);
        }
        $additionalAttributes = [];

        foreach ($additionalAttributeCodes as $key) {
            if (array_key_exists($key, $productData)) {
                $additionalAttributes[$key] = $productData[$key];
            }
            unset($productData[$key]);
        }

        if ($additionalAttributes) {
            $productData[DefaultProductExport::COL_ADDITIONAL_ATTRIBUTES] = $additionalAttributes;
        }

        return $productData;
    }
        
    /**
     * @param array $productData
     * @return array
     */
    protected function prepareExistProduct(array $productData)
    {
        if (!empty($productData[ProductAttributeInterface::CODE_STATUS])
            && $productData[ProductAttributeInterface::CODE_STATUS] == ProductStatus::STATUS_DISABLED
        ) {
            $this->disabledProductsCount++;
        }
        $sku = $productData[ProductInterface::SKU];
        $currentStatus = $this->getExistSkus()[$sku][ProductConstants::PRODUCT_ATTRIBUTE_STOCK_STATUS];
        $newStatus = $productData[ProductConstants::PRODUCT_ATTRIBUTE_STOCK_STATUS];

        if ($newStatus != $currentStatus) {
            $this->logger->warning(
                __(
                    'Stock status is changed for sku: %1. Previous status: %2. New status: %3',
                    $sku,
                    $currentStatus,
                    $newStatus
                ),
                [],
                \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
            );
        }

        $currentCondition = $this->getExistSkus()[$sku][ProductConstants::PRODUCT_ATTRIBUTE_STOCK_CONDITION];
        $newCondition = $productData[ProductConstants::PRODUCT_ATTRIBUTE_STOCK_CONDITION];
        if ($newCondition != $currentCondition) {
            $this->logger->warning(
                __(
                    'Stock condition is changed for sku: %1. Previous condition: %2. New condition: %3',
                    $sku,
                    $currentCondition,
                    $newCondition
                ),
                [],
                \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
            );
        }
        $productData = $this->getAttributeSetForExistProduct($productData);
        unset($this->existSkus[$sku]);
        unset($productData[ProductInterface::NAME]);
        return $productData;
    }

    /**
     * Business rule:
     * If is disabling more than 10% existing products,
     * the changes will be reversed and an error will be logged in Magento Abstract Integration logs
     * @return bool
     */
    protected function checkDisabledPercent()
    {
        if (!$this->countMagentoSkus) {
            return false;
        }

        $existsCount = count($this->existSkus);
        if ($this->disabledProductsCount || $existsCount) {
            $skusToDisable = $this->disabledProductsCount + $existsCount;
            $disabledPercent = round($skusToDisable / $this->countMagentoSkus * 100);
            if ($disabledPercent <= 20){
                return false;
            }
            if ($disabledPercent > $this->configHelper->getProductsDisabledPercent()) {
                $this->logger->warning(
                    __(
                        'Update for exists products is skipped because will be disabled %1 percent.
                         Disabled Products Count: %2, Exist in Magento but not received from Pronto: %3, 
                         Sku not received from Pronto: %4',
                        $disabledPercent,
                        $this->disabledProductsCount,
                        $existsCount,
                        implode(', ', array_keys($this->existSkus))
                    ),
                    [],
                    \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
                );
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getBackorders(array $data)
    {
        if (!empty($data[ProductConstants::PRODUCT_ATTRIBUTE_STOCK_CONDITION])
            && $data[ProductConstants::PRODUCT_ATTRIBUTE_STOCK_CONDITION] == 'T') {
            $data[self::BACKORDERS] = 0;
            $data[self::USE_CONFIG_BACKORDERS] = 0;
        }
        return $data;
    }

    /**
     * @return string
     */
    protected function getDefaultAttributeSetName()
    {
        if ($this->defaultAttributeSetName === null) {
            $this->defaultAttributeSetName = $this->productResource->getDefaultAttributeSetName();
        }
        return $this->defaultAttributeSetName;
    }
}
