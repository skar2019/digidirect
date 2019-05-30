<?php

namespace Ewave\ProntoDigi\ProntoApi;

use Ewave\AI\Model\Lib\Mapping\MapperInterface;
use Ewave\AI\Model\Lib\Validator\Validate;
use Ewave\Pronto\ProntoApi\ResponseHandler as BaseResponseHandler;
use Ewave\ProntoDigi\Helper\Config;
use Ewave\ProntoDigi\Helper\Inventory;
use Ewave\ProntoDigi\Model\Import\Sources\SourceItems as SourceItemsImport;
use Ewave\ProntoDigi\Model\ResourceModel\Product as ProductResource;
use Ewave\ProntoDigi\ProntoApi\Constants\InventoryGetRequest;
use Ewave\ProntoDigi\ProntoApi\Constants\Products as ProductConstants;
use Ewave\ProntoDigi\ProntoApi\Products\Get\Response\CategoryProcessor;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Url;
use Magento\Framework\Exception\LocalizedException;
use Magento\Inventory\Model\SourceItemFactory;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryConfigurationApi\Model\IsSourceItemManagementAllowedForProductTypeInterface;

/**
 * Class ProductResponseHandlerAbstract
 * @package Ewave\ProntoDigi\ProntoApi
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
abstract class ProductResponseHandlerAbstract extends BaseResponseHandler implements \Ewave\Pronto\ProntoApi\ResponseHandlerMultipleInterface
{
    /**
     * @var Validate
     */
    protected $validator;

    /**
     * @var array|null
     */
    protected $existSkus;

    /**
     * @var array|null
     */
    protected $excludedSkus;

    /**
     * @var array|null
     */
    protected $existSources;

    /**
     * @var array|null
     */
    protected $existSourceItems;

    /**
     * @var int
     */
    protected $disabledProductsCount = 0;

    /**
     * @var int
     */
    protected $countMagentoSkus = 0;

    /**
     * @var Url
     */
    protected $productUrl;

    /**
     * @var ProductResource
     */
    protected $productResource;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $rootCategory;

    /**
     * @var CategoryProcessor
     */
    protected $categoryProcessor;

    /**
     * @var array
     */
    protected $attributeSetList = [];

    /**
     * @var Inventory
     */
    protected $inventoryHelper;

    /**
     * @var IsSourceItemManagementAllowedForProductTypeInterface
     */
    protected $allowedForProductType;

    /**
     * @var SourceItemsImport
     */
    protected $sourceItemsImport;

    /**
     * @var array
     */
    protected $sourceItemsToUpdate = [];

    /**
     * @var array
     */
    protected $existsProducts = [];

    /**
     * @var array
     */
    protected $newProducts = [];

    /**
     * @var array
     */
    protected $excludeQtyFromUpdate = [];

    /**
     * @var SourceItemFactory
     */
    protected $sourceItemFactory;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * ProductResponseHandlerAbstract constructor.
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
        MapperInterface $mapper = null
    ) {
        parent::__construct($mapper);
        $this->validator = $validator;
        $this->productUrl = $productUrl;
        $this->productResource = $productResource;
        $this->categoryProcessor = $categoryProcessor;
        $this->categoryFactory = $categoryFactory;
        $this->inventoryHelper = $inventoryHelper;
        $this->allowedForProductType = $allowedForProductType;
        $this->sourceItemsImport = $sourceItemsImport;
        $this->sourceItemFactory = $sourceItemFactory;
        $this->configHelper = $configHelper;
    }

    /**
     * @return array
     */
    public function getExcludedSkus()
    {
        if ($this->excludedSkus === null) {
            $this->excludedSkus = $this->productResource->getExcludedSkus();
        }
        return $this->excludedSkus;
    }

    /**
     * NOTE: HERE WE GET ONLY PRODUCTS THAT HAVE ONLY DIGITS IN SKU!
     *
     * @return array|null
     */
    public function getExistSkus()
    {
        if (empty($this->existSkus)) {
            $this->existSkus = $this->productResource->getExistSkus();
            foreach ($this->existSkus as $sku => $productInfo) {
                if (!ctype_digit($sku)) {
                    unset($this->existSkus[$sku]);
                }
            }
            $this->countMagentoSkus = count($this->existSkus);
        }
        return $this->existSkus;
    }

    /**
     * @return array|\Magento\InventoryApi\Api\Data\SourceInterface[]|null
     */
    public function getExistSources()
    {
        if (empty($this->existSources)) {
            $this->existSources = $this->inventoryHelper->getSources();
        }
        return $this->existSources;
    }

    /**
     * @return array|null
     */
    public function getExistSourceItems()
    {
        if (empty($this->existSourceItems)) {
            $this->existSourceItems = $this->inventoryHelper->getSourceItemsData($this->getExistSkus());
        }
        return $this->existSourceItems;
    }

    /**
     * @return array
     */
    public function getAttributeSetList()
    {
        if (empty($this->attributeSetList)) {
            $attributeSetList = $this->productResource->getAttributeSetList();
            foreach ($attributeSetList as $attributeSet) {
                $this->attributeSetList[$this->standardizeKey($attributeSet)] = $attributeSet;
            }
        }
        return $this->attributeSetList;
    }

    /**
     * @return SourceItemsImport|void
     * @throws LocalizedException
     */
    protected function saveSourceItems()
    {
        $importData = [];
        foreach ($this->sourceItemsToUpdate as $sources) {
            foreach ($sources as $sourceItem) {
                $importData[] = $sourceItem;
            }
        }
        return $this->sourceItemsImport->updateData($importData);
    }

    /**
     * @return $this
     */
    protected function unsetData()
    {
        $this->excludedSkus = null;
        $this->existSources = [];
        $this->existSourceItems = [];
        $this->newProducts = [];
        return $this;
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
            || (!empty($data[ProductConstants::PRODUCT_ATTRIBUTE_STOCK_CONDITION])
                && $data[ProductConstants::PRODUCT_ATTRIBUTE_STOCK_CONDITION] != 'O')
        ) {
            $data[ProductAttributeInterface::CODE_STATUS] = ProductStatus::STATUS_DISABLED;
        }
        return $data;
    }

    /**
     * @param string $message
     * @param mixed $productData
     */
    protected function logInvalidProductInfo($message, $productData)
    {
        $this->logger->warning(
            __('Invalid product. Errors: %1', $message),
            is_array($productData) ? $productData : [$productData],
            \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
        );
    }

    /**
     * @param string $key
     * @return string
     */
    protected function standardizeKey($key)
    {
        return trim(strtolower($key));
    }

    /**
     * @param array $data
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function getInventorySources(array $data)
    {
        $sku = $data[ProductInterface::SKU];
        $productDataSources = $data['sources'];
        $stockStatus = $data[ProductConstants::PRODUCT_ATTRIBUTE_STOCK_STATUS];
        $validSources = [];
        foreach ($productDataSources as $key => $item) {
            //case 3
            if ($item[SourceItemInterface::SOURCE_CODE] == "") {
                $this->logger->warning(
                    __(
                        'Invalid source code for sku: %1.',
                        $sku
                    ),
                    $data,
                    \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
                );
                unset($productDataSources[$key]);
                continue;
            }

            //case 5
            if (!isset($this->getExistSources()[$item[SourceItemInterface::SOURCE_CODE]])) {
                $this->logger->warning(
                    __(
                        'Source code %1 is not matching with existing Sources for sku: %2.',
                        $item['source_code'],
                        $sku
                    ),
                    $data,
                    \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
                );
                unset($productDataSources[$key]);
                continue;
            }

            //case 4
            if ($item['qty'] === "") {
                $data[ProductAttributeInterface::CODE_STATUS] = ProductStatus::STATUS_DISABLED;
                $this->logger->warning(
                    __(
                        'Invalid qty for source code: %1. Product %2 is disabled.',
                        $item[SourceItemInterface::SOURCE_CODE],
                        $sku
                    ),
                    $data,
                    \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
                );
                $this->excludeQtyFromUpdate[$sku][$item[SourceItemInterface::SOURCE_CODE]] =
                    $item[SourceItemInterface::SOURCE_CODE];
            }
        }

        if (empty($productDataSources)) {
            $data[ProductAttributeInterface::CODE_STATUS] = ProductStatus::STATUS_DISABLED;
            $this->logger->warning(
                __(
                    'All source codes are invalid for product: %1. Product is disabled.',
                    $sku
                ),
                $data,
                \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
            );
        }

        foreach ($productDataSources as $key => $item) {
            $validSources[$item[SourceItemInterface::SOURCE_CODE]] = $key;
        }

        //case 6
        if (
            !empty($this->getExistSourceItems()[$sku]) &&
            $notMappedSourceCodes = array_diff(
                array_keys($this->getExistSourceItems()[$sku]),
                array_keys($validSources)
            )
        ) {
            foreach ($notMappedSourceCodes as $code) {
                $productDataSources[] = [
                    SourceItemInterface::SOURCE_CODE => $code,
                    'qty' => 0
                ];
            }
            $this->logger->warning(
                __(
                    'Source codes: %1 are missed for product %2. Qty is decreased to 0.',
                    implode(',', $notMappedSourceCodes),
                    $sku
                ),
                $data,
                \Ewave\AI\Model\Logger\Logger::LOG_PLACE_FILE
            );
        }
        foreach ($productDataSources as $dataSource) {
            $this->sourceItemsToUpdate[$sku][] = $this->prepareSourceItemDataForDb($sku, $dataSource, $stockStatus);
        }
        $data['sources'] = isset($this->sourceItemsToUpdate[$sku]) ?
            $this->sourceItemsToUpdate[$sku] :
            $productDataSources;
        return $data;
    }

    /**
     * @param array $data
     * @return array|mixed
     */
    protected function assignProductToExistedSources(array $data)
    {
        $sku = $data[ProductInterface::SKU];
        $stockStatus = $data[ProductConstants::PRODUCT_ATTRIBUTE_STOCK_STATUS];
        $sourceToUpdate = [];
        $sourceItemsToUpdate = isset($this->sourceItemsToUpdate[$sku]) ? $this->sourceItemsToUpdate[$sku] : [];

        foreach ($sourceItemsToUpdate as $source) {
            $sourceToUpdate[$source[SourceItemInterface::SOURCE_CODE]] = $source[SourceItemInterface::SOURCE_CODE];
        }
        foreach ($this->getExistSources() as $code => $source) {
            if (!isset($sourceToUpdate[$code])) {
                $sourceItemsToUpdate[] = $this->prepareSourceItemDataForDb($sku, $source, $stockStatus, 0);
            }
        }
        $this->sourceItemsToUpdate[$sku] = $sourceItemsToUpdate;
        $data['sources'] = $sourceItemsToUpdate;
        return $data;
    }

    /**
     * @param string $sku
     * @param array $data
     * @param string $stockStatus
     * @param null|int $qty
     * @return array
     */
    protected function prepareSourceItemDataForDb($sku, $data, $stockStatus, $qty = null)
    {
        if (
            isset($this->excludeQtyFromUpdate[$sku][$data[SourceItemInterface::SOURCE_CODE]]) &&
            isset($this->existSourceItems[$sku][$data[SourceItemInterface::SOURCE_CODE]])
        ) {
            $item = $this->existSourceItems[$sku][$data[SourceItemInterface::SOURCE_CODE]];
            $qty = $item->getQuantity();
        }

        if ($qty === null) {
            $qty = $data['qty'];
            $qty -= $data[SourceItemInterface::SOURCE_CODE] == InventoryGetRequest::SWHS_SOURCE_CODE ?
                InventoryGetRequest::SWHS_QTY_DECREADE_VALUE :
                InventoryGetRequest::DEFAULT_QTY_DECREADE_VALUE;
        }

        $sourceItemData = [
            SourceItemInterface::SOURCE_CODE => $data[SourceItemInterface::SOURCE_CODE],
            SourceItemInterface::QUANTITY => $qty > 0 ? $qty : 0,
            SourceItemInterface::STATUS => $qty <= 0 && ($stockStatus != InventoryGetRequest::VIRTUAL_PRODUCT_FLAG
                && $stockStatus != InventoryGetRequest::SPECIAL_ORDER_FLAG) ? 0 : 1,
            SourceItemInterface::SKU => $sku
        ];

        return $this->sourceItemFactory->create(['data' => $sourceItemData]);
    }

    /**
     * @param string $sku
     * @return $this
     */
    protected function filterSourceItemsBeforeUpdate($sku)
    {
        if (isset($this->sourceItemsToUpdate[$sku]) && isset($this->getExistSourceItems()[$sku])) {
            $readyToUpdate = $this->sourceItemsToUpdate[$sku];
            $existedSourceItems = $this->getExistSourceItems()[$sku];
            foreach ($readyToUpdate as $key => $sourceItem) {
                if (
                    isset($existedSourceItems[$sourceItem->getSourceCode()]) &&
                    $existedSourceItems[$sourceItem->getSourceCode()]->getQuantity() == $sourceItem->getQuantity() &&
                    $existedSourceItems[$sourceItem->getSourceCode()]->getStatus() == $sourceItem->getStatus()
                ) {
                    unset($readyToUpdate[$key]);
                }
            }
            $this->sourceItemsToUpdate[$sku] = $readyToUpdate;
            if (empty($readyToUpdate)) {
                unset($this->sourceItemsToUpdate[$sku]);
            }
        }
        return $this;
    }

    /**
     * @param string $productType
     * @return bool
     */
    protected function isAllowedMultiSourceInventoryForProductType($productType)
    {
        return $this->allowedForProductType->execute($productType);
    }
}
