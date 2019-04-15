<?php

namespace Ewave\ProntoDigi\ProntoApi\Products\Get;

use Ewave\AI\Model\Lib\Import\Product\Entity;
use Ewave\ProntoDigi\ProntoApi\Constants\Products as ProductConstants;
use Ewave\ProntoDigi\ProntoApi\ProductResponseHandlerAbstract;
use Ewave\ProntoDigi\ProntoApi\Products\Get\Response\CategoryProcessor;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\CatalogImportExport\Model\Import\Product;
use Magento\Framework\Exception\LocalizedException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ResponseHandler extends ProductResponseHandlerAbstract
{
    /**
     * @var array
     */
    protected $productsToImport = [];

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
            $this->existsProducts = [];
        }
        $this->unsetData();
        $this->import($this->productsToImport);

        return $this;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    protected function import($products)
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

                    if (!isset($this->getExistSkus()[$sku])) {
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
                        $this->newProducts[$sku] = $productData;
                    } elseif ($this->isProductUpdatePossible($productData)) {
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

        $products = array_merge($this->newProducts, $this->existsProducts);
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
}
