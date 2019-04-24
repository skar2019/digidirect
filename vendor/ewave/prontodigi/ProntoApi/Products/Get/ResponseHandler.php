<?php

namespace Ewave\ProntoDigi\ProntoApi\Products\Get;

use Ewave\AI\Model\Lib\Import\Product\Entity;
use Ewave\AI\Model\Lib\Import\Product\EntityFactory as ImportFactory;
use Ewave\AI\Model\Lib\Mapping\MapperInterface;
use Ewave\AI\Model\Lib\Validator\Validate;
use Ewave\ProntoDigi\Helper\Inventory;
use Ewave\ProntoDigi\Model\Import\Sources\SourceItems as SourceItemsImport;
use Ewave\ProntoDigi\Model\ResourceModel\Product as ProductResource;
use Ewave\ProntoDigi\ProntoApi\Constants\Products as ProductConstants;
use Ewave\ProntoDigi\ProntoApi\ProductResponseHandlerAbstract;
use Ewave\ProntoDigi\ProntoApi\Products\Get\Response\CategoryProcessor;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Url;
use Magento\Catalog\Model\ResourceModel\Attribute as AttributeResource;
use Magento\CatalogImportExport\Model\Import\Product;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Exception\LocalizedException;
use Magento\Inventory\Model\SourceItemFactory;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;
use Magento\Catalog\Model\Product as ProductModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ResponseHandler extends ProductResponseHandlerAbstract
{
    const BRAND_ATTRIBUTE_CODE = 'brand';

    /**
     * @var array
     */
    protected $productsToImport = [];

    /**
     * @var AttributeResource
     */
    protected $attributeResource;

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * ResponseHandler constructor.
     * @param EavConfig $eavConfig
     * @param AttributeResource $attributeResource
     * @param ImportFactory $importFactory
     * @param Validate $validator
     * @param Url $productUrl
     * @param ProductResource $productResource
     * @param CategoryFactory $categoryFactory
     * @param CategoryProcessor $categoryProcessor
     * @param Inventory $inventoryHelper
     * @param IsSourceItemManagementAllowedForProductTypeInterface $allowedForProductType
     * @param SourceItemsImport $sourceItemsImport
     * @param SourceItemFactory $sourceItemFactory
     * @param MapperInterface|null $mapper
     */
    public function __construct(
        EavConfig $eavConfig,
        AttributeResource $attributeResource,
        ImportFactory $importFactory,
        Validate $validator,
        Url $productUrl,
        ProductResource $productResource,
        CategoryFactory $categoryFactory,
        CategoryProcessor $categoryProcessor,
        Inventory $inventoryHelper,
        IsSourceItemManagementAllowedForProductTypeInterface $allowedForProductType,
        SourceItemsImport $sourceItemsImport,
        SourceItemFactory $sourceItemFactory,
        MapperInterface $mapper = null
    ) {
        $this->eavConfig = $eavConfig;
        $this->attributeResource = $attributeResource;
        parent::__construct($importFactory, $validator, $productUrl, $productResource, $categoryFactory, $categoryProcessor, $inventoryHelper, $allowedForProductType, $sourceItemsImport, $sourceItemFactory, $mapper);
    }

    /**
     * @param array $response
     * @return array|ProductResponseHandlerAbstract
     */
    public function handle(array $response)
    {
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
        $this->unsetData();
        $this->import($this->productsToImport);
        return $this;
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
        /** @var \Ewave\AI\Model\Lib\Import\Product\Entity $import */
        $import = $this->importFactory->create(['aiLogger' => $this->logger]);
        $import->setProductsData([$products]);
        $import->saveProducts();
        $this->saveSourceItems();
        $attribute = $this->eavConfig->getAttribute(ProductModel::ENTITY, self::BRAND_ATTRIBUTE_CODE);
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
                $this->existSkus,
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
        $productsUrlKey = [];
        $newProducts = [];
        $existsProducts = [];
        foreach ($response as $k => $productData) {
            if ($this->validator->isValid($productData)) {
                try {
                    $productData = $this->mapData($productData);
                    $sku = $productData[ProductInterface::SKU];
                    $name = $productData[ProductInterface::NAME];

                    if ($this->isAllowedMultiSourceInventoryForProductType($productData['product_type'])) {
                        $productData = $this->getInventorySources($productData);
                    } else {
                        unset($productData['sources']);
                    }
                    $productData = $this->getStatus($productData);
                    $productData = $this->getCategories($productData);
                    $productData = $this->getAttributeSet($productData);

                    if (!isset($this->getExistSkus()[$sku]) && empty($this->getExcludedSkus()[$sku])) {
                        $productData[ProductAttributeInterface::CODE_STATUS] = ProductStatus::STATUS_DISABLED;
                        if ($this->isAllowedMultiSourceInventoryForProductType($productData['product_type'])) {
                            $productData['sources'] = $this->assignProductToExistedSources($productData);
                        } else {
                            unset($productData['sources']);
                        }

                        $urkKey = $this->productUrl->formatUrlKey($name);
                        if (isset($productsUrlKey[$urkKey])) {
                            $urkKey = $urkKey . '-' . $sku;
                        }
                        $productsUrlKey[$urkKey] = $sku;
                        $productData[Entity::URL_KEY] = $urkKey;
                        $newProducts[$sku] = $productData;
                    } elseif ($this->isProductUpdatePossible($productData)) {
                        $this->filterSourceItemsBeforeUpdate($sku);
                        $existsProducts[$sku] = $this->prepareExistProduct($productData);
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
                    'Skipped update because product is excluded from the integration, SKU %1',
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
                    'product %1 type could not be changed: old product type %2, new product type %3',
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
        $categoryAttributes = ['category1', 'category2', 'category3', 'category4'];
        $existsCategories = $this->categoryProcessor->getCategories();
        reset($existsCategories);
        $categoryPath = key($existsCategories);
        $fullCategoryPath = $categoryPath;
        foreach ($categoryAttributes as $catagoryAttribute) {
            if (isset($data[$catagoryAttribute])) {
                $productCategory = $data[$catagoryAttribute];
                $fullCategoryPath .= CategoryProcessor::DELIMITER_CATEGORY . $productCategory;
                $newPath = $this->standardizeKey(
                    $categoryPath . CategoryProcessor::DELIMITER_CATEGORY . $productCategory
                );
                if (isset($existsCategories[$newPath])) {
                    $categoryPath = $newPath;
                }
            }
        }

        $data['categories'] = $categoryPath;
        $this->categoryProcessor->upsertCategories($fullCategoryPath, ',');
        return $data;
    }

    /**
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    protected function getAttributeSet(array $data)
    {
        $standardizedKey = $this->standardizeKey($data[Product::COL_ATTR_SET]);
        if (!isset($this->getAttributeSetList()[$standardizedKey])) {
            throw new LocalizedException(
                __(
                    'Attribute set %1 does not exist. Product %2 skipped',
                    $data[Product::COL_ATTR_SET],
                    $data[ProductInterface::SKU]
                )
            );
        }

        $data[Product::COL_ATTR_SET] = $this->getAttributeSetList()[$standardizedKey];
        return $data;
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
                []
            );
        }

        $currentCondigion = $this->getExistSkus()[$sku][ProductConstants::PRODUCT_ATTRIBUTE_STOCK_CONDITION];
        $newCondition = $productData[ProductConstants::PRODUCT_ATTRIBUTE_STOCK_CONDITION];
        if ($newStatus != $currentStatus) {
            $this->logger->warning(
                __(
                    'Stock condition is changed for sku: %1. Previous condition: %2. New condition: %3',
                    $sku,
                    $currentCondigion,
                    $newCondition
                ),
                []
            );
        }
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
        $existsCount = count($this->existSkus);
        if ($this->disabledProductsCount || $existsCount) {
            $skusToDisable = $this->disabledProductsCount + $existsCount;
            $disabledPercent = round($skusToDisable / $this->countMagentoSkus * 100);
            if ($disabledPercent > self::DISABLED_PRODUCTS_PERCENT_FOR_SKIP_UPDATE) {
                $this->logger->warning(
                    __(
                        'Update for exists products is skipped because will be disabled %1 percent.
                         Disabled Products Count: %2, Exist in Magento but not received from Pronto: %3',
                        $disabledPercent,
                        $this->disabledProductsCount,
                        $existsCount
                    ),
                    [],
                    \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
                );
                return true;
            }
        }
        return false;
    }
}
