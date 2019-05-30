<?php

namespace Ewave\ProntoDigi\ProntoApi\Inventory\Get;

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
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Url;
use Magento\Framework\Exception\LocalizedException;
use Magento\Inventory\Model\SourceItemFactory;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;

/**
 * Class ResponseHandler
 * @package Ewave\ProntoDigi\ProntoApi\Inventory\Get
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ResponseHandler extends ProductResponseHandlerAbstract
{
    /**
     * @var array
     */
    protected $attributesToUpdate;

    /**
     * ResponseHandler constructor.
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
     * @param array $attributesToUpdate
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
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
        array $attributesToUpdate = []
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
        $this->attributesToUpdate = $attributesToUpdate;
    }

    /**
     * @param array $response
     * @return array|ProductResponseHandlerAbstract
     */
    public function handle(array $response)
    {
        return $this->prepareProductsData($response);
    }

    /**
     * @return $this|mixed
     * @throws LocalizedException
     */
    public function finalize()
    {
        if ($this->checkDisabledPercent()) {
            $this->existsProducts = [];
        }
        $this->unsetData();
        $this->update($this->existsProducts);

        return $this;
    }

    /**
     * @param array $products
     * @return $this|ProductResponseHandlerAbstract
     * @throws LocalizedException
     */
    protected function update($products)
    {
        if (empty($products)) {
            $this->logger->info(__('There are no valid items to update.'));
            return $this;
        }

        $start = microtime(true);
        $this->saveProductAttributes($products);
        $this->saveSourceItems();

        $this->logger->info(
            'TIME LOG: Import time: ' . round(microtime(true) - $start, 3) . ' sec.'
        );

        return $this;
    }

    /**
     * @param array $response
     * @return array
     */
    protected function prepareProductsData(array $response)
    {
        foreach ($response as $k => $productData) {
            if ($this->validator->isValid($productData)) {
                try {
                    $productData = $this->mapData($productData);
                    $sku = $productData[ProductInterface::SKU];
                    if (!isset($this->getExistSkus()[$sku])) {
                        $this->logger->info(
                            __(
                                'Skipped update: product "%1" does not exist',
                                $sku
                            ),
                            $productData,
                            \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
                        );
                        unset($response[$k]);
                        continue;
                    }
                    if ($this->isAllowedMultiSourceInventoryForProductType($this->getExistSkus()[$sku]['type_id'])) {
                        $productData = $this->getInventorySources($productData);
                    } else {
                        unset($productData['sources']);
                    }
                    $productData = $this->getStatus($productData);
                    if ($this->isProductUpdatePossible($productData)) {
                        $this->filterSourceItemsBeforeUpdate($sku);
                        $this->existsProducts[$sku] = $this->prepareExistProduct($productData);
                    }
                } catch (LocalizedException $e) {
                    $this->logInvalidProductInfo($e->getMessage(), $productData);
                }
            } else {
                $this->logInvalidProductInfo(implode('. ', $this->validator->getMessages()), $productData);
            }
            unset($response[$k]);
        }

        return $this->existsProducts;
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

        return true;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getStatus(array $data)
    {
        if (empty($data[ProductAttributeInterface::CODE_PRICE])
            || empty($data['sources'])
            || empty($data[ProductConstants::PRODUCT_ATTRIBUTE_STOCK_STATUS])
        ) {
            $data[ProductAttributeInterface::CODE_STATUS] = ProductStatus::STATUS_DISABLED;
        }
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

        unset($this->existSkus[$sku]);
        return $productData;
    }

    /**
     * @param array $products
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function saveProductAttributes($products)
    {
        return $this->productResource->updateProductAttributes($products, $this->attributesToUpdate);
    }

    /**
     * Business rule:
     * If is disabling more than 10% existing products,
     * the changes will be reversed and an error will be logged in Magento Abstract Integration logs
     * @return bool
     */
    protected function checkDisabledPercent()
    {
        if ($this->disabledProductsCount && $this->countMagentoSkus) {
            $disabledPercent = round($this->disabledProductsCount / $this->countMagentoSkus * 100);
            if ($disabledPercent > $this->configHelper->getInventoryProductsDisabledPercent()) {
                $this->logger->warning(
                    __(
                        'Update for exists products is skipped because will be disabled %1 percents',
                        $disabledPercent
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
     * @return ProductResponseHandlerAbstract|$this
     */
    protected function unsetData()
    {
        $this->existSkus = [];
        return parent::unsetData();
    }
}
