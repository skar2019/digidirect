<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_PreOrder
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\PreOrder\Helper;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends AbstractHelper
{
    const ORDER_NO = 0;
    const ORDER_YES = 1;
    const ORDER_OUT_OF_STOCK = 2;

    const DISPLAY_OOS_PATH_CONFIG = 'preorder/general/display_oos_with_pre_status_only';
    const ENABLE_MODULE_CONFIGURABLE_GRID_VIEW = "configuablegridview/general/active";
    const LIST_PREORDER_ATTRIBUTES = [
        'preorder',
        'pre_oder_from_date',
        'pre_oder_to_date',
        'message',
        'availability_message'
    ];

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface
     */
    protected $stockItemRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Bss\PreOrder\Model\ResourceModel\PreOrder
     */
    protected $preOrderResource;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $resourceProduct;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Bss\PreOrder\Model\Factory
     */
    protected $multiSourceInventory;

    /**
     * Data constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $stockItemRepository
     * @param \Bss\PreOrder\Model\ResourceModel\PreOrder $preOrderResource
     * @param \Magento\Catalog\Model\ResourceModel\Product $resourceProduct
     * @param ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param ProductFactory $productFactory
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Bss\PreOrder\Model\Factory $multiSourceInventory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $stockItemRepository,
        \Bss\PreOrder\Model\ResourceModel\PreOrder $preOrderResource,
        \Magento\Catalog\Model\ResourceModel\Product $resourceProduct,
        ProductMetadataInterface $productMetadata,
        \Magento\Framework\Module\Manager $moduleManager,
        ProductFactory $productFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Bss\PreOrder\Model\Factory $multiSourceInventory
    ) {
        $this->registry = $registry;
        parent::__construct($context);
        $this->stockItemRepository = $stockItemRepository;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->timezone = $timezone;
        $this->preOrderResource = $preOrderResource;
        $this->resourceProduct = $resourceProduct;
        $this->productMetadata = $productMetadata;
        $this->moduleManager = $moduleManager;
        $this->productFactory = $productFactory;
        $this->serializer = $serializer;
        $this->multiSourceInventory = $multiSourceInventory;
    }

    /**
     * @param mixed $product
     * @param int $productId
     * @param float $itemQtyOrdered
     * @return int|float|null
     */
    public function getProductSalableQty($product, $productId, $itemQtyOrdered = 0)
    {
        if ($this->checkVersion()) {
            $qtyProduct = $this->getStockItem($productId)->getQty() + $itemQtyOrdered;
        } else {
            $qtyProduct = $this->getSalableQty($product->getSku()) + $itemQtyOrdered;
        }
        return $qtyProduct ? $qtyProduct : 0;
    }

    /**
     * @param string|null $sku
     * @return int|float
     */
    public function getSalableQty($sku)
    {
        $qtySalable = 0;
        $data = $this->multiSourceInventory->getSalableQtyBySku()->execute($sku);
        if ($data && is_array($data)) {
            foreach ($data as $stockSource) {
                if (isset($stockSource['qty'])) {
                    $qtySalable += $stockSource['qty'];
                }
            }
        }
        return $qtySalable;
    }

    /**
     * @return mixed
     */
    public function serializeClass()
    {
        return $this->serializer;
    }

    /**
     * Get Pre Order By Product Id
     *
     * @param int $productId
     * @param int|null $storeId
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPreOrder($productId, $storeId = null)
    {
        $storeId = $storeId ? $storeId : $this->storeManager->getStore();
        $preOrderData = $this->resourceProduct->getAttributeRawValue(
            $productId,
            'preorder',
            $storeId
        );
        return $preOrderData ? $preOrderData : 0;
    }

    /**
     * Get Product By Id
     *
     * @param int $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductItem($productId)
    {
        return $this->productRepository->getById($productId);
    }

    /**
     * Get Product By Sku
     *
     * @param string $sku
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductBySku($sku)
    {
        return $this->productRepository->get($sku);
    }

    /**
     * Get product by id
     *
     * @param int $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductById($productId)
    {
        try {
            return $this->productRepository->getById($productId);
        } catch (\Exception $exception) {
            return '';
        }
    }

    /**
     * Compare Version with 2.3.0
     *
     * @return bool
     */
    public function checkVersion()
    {
        $magentoVersion = $this->productMetadata->getVersion();
        $msiEnable = $this->moduleManager->isOutputEnabled('Magento_Inventory');
        if (!$msiEnable) {
            return true;
        }
        return version_compare($magentoVersion, '2.3.0', '<');
    }

    /**
     * Check Stock Status Product
     *
     * @param int $productId
     * @return bool|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getIsInStock($productId)
    {
        if ($this->checkVersion()) {
            return $this->stockItemRepository->getStockItem($productId, $this->getStoreId())->getIsInStock();
        }
        return $this->productRepository->getById($productId)->isAvailable();
    }

    /**
     * @param $productId
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStockItem($productId)
    {
        return $this->stockItemRepository->getStockItem($productId, $this->getStoreId());
    }

    /**
     * Get Store Id
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Is Enable Module
     *
     * @return bool
     */
    public function isEnable()
    {
        return $this->scopeConfig->isSetFlag(
            'preorder/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Is Available to Pre order
     *
     * @param int $productId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isAvailablePreOrder($productId)
    {
        $fromDateStr = $this->resourceProduct->getAttributeRawValue(
            $productId,
            'pre_oder_from_date',
            $this->storeManager->getStore()
        );
        $toDateStr = $this->resourceProduct->getAttributeRawValue(
            $productId,
            'pre_oder_to_date',
            $this->storeManager->getStore()
        );
        return $this->isAvailablePreOrderFromFlatData($fromDateStr, $toDateStr);
    }

    /**
     * @param string $fromDateStr
     * @param string $toDateStr
     * @return bool
     */
    public function isAvailablePreOrderFromFlatData($fromDateStr, $toDateStr)
    {
        $fromDate = $fromDateStr ? strtotime($fromDateStr) : false;
        $toDate = $toDateStr ? strtotime($toDateStr) : false;
        $currentDate = strtotime($this->timezone->date()->format('Y-m-d'));
        return $this->checkisAvailableDate($fromDate, $toDate, $currentDate);
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     * @param string $currentDate
     * @return bool
     */
    private function checkisAvailableDate($fromDate, $toDate, $currentDate)
    {
        if ((!$fromDate && !$toDate) || ($currentDate <= $toDate && $currentDate >= $fromDate) ||
            ($currentDate <= $toDate && !$fromDate) || ($currentDate >= $fromDate && !$toDate)
        ) {
            return true;
        }
        return false;
    }

    /**
     * @param int $productId
     * @param null $storeId
     * @return array|bool|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPreOrderFromDate($productId, $storeId = null)
    {
        $storeId = $storeId ? $storeId : $this->storeManager->getStore();
        $fromDateStr = $this->resourceProduct->getAttributeRawValue(
            $productId,
            'pre_oder_from_date',
            $storeId
        );
        return $fromDateStr ? $this->formatDate($fromDateStr) : '';
    }

    /**
     * @param int $productId
     * @param null $storeId
     * @return array|bool|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPreOrderToDate($productId, $storeId = null)
    {
        $storeId = $storeId ? $storeId : $this->storeManager->getStore();
        $toDateStr = $this->resourceProduct->getAttributeRawValue(
            $productId,
            'pre_oder_to_date',
            $storeId
        );
        return $toDateStr ? $this->formatDate($toDateStr) : '';
    }

    /**
     * Is Mixed Order
     *
     * @return bool
     */
    public function isMix()
    {
        return $this->scopeConfig->isSetFlag(
            'preorder/general/mix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Button Html Text
     *
     * @return string
     */
    public function getButton()
    {
        return $this->scopeConfig->getValue(
            'preorder/general/button',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Message Pre Order
     *
     * @return string
     */
    public function getMess()
    {
        return $this->scopeConfig->getValue(
            'preorder/general/mess',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Note Pre Order
     *
     * @return string
     */
    public function getNote()
    {
        return $this->scopeConfig->getValue(
            'preorder/general/note',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Cart Message
     *
     * @return string
     */
    public function getCartMess()
    {
        return $this->scopeConfig->getValue(
            'preorder/general/cartmess',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Display Out Of Stock With Pre-Order Only
     *
     * @return mixed
     */
    public function getDisplayOutOfStock()
    {
        return $this->scopeConfig->getValue(
            self::DISPLAY_OOS_PATH_CONFIG,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isDisplayOutOfStockProduct()
    {
        $displayOutOfStock = $this->getDisplayOutOfStock();
        $displayOutOfStockCore = $this->scopeConfig->getValue(
            \Magento\CatalogInventory\Model\Configuration::XML_PATH_SHOW_OUT_OF_STOCK,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($displayOutOfStockCore && $displayOutOfStock) {
            return true;
        }
        return false;
    }

    /**
     * Format Date
     *
     * @param string $date
     * @param int $format
     * @param bool $showTime
     * @param string $timezone
     * @param string $pattern
     * @return bool|string
     */
    public function formatDate(
        $date,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null,
        $pattern = 'd MMM Y'
    ) {
        if ($date) {
            return $this->timezone->formatDateTime(
                $date,
                $format,
                $showTime ? $format : \IntlDateFormatter::NONE,
                null,
                $timezone,
                $pattern
            );
        }

        return false;
    }

    /**
     * Get Mess
     *
     * @param string $mess
     * @param string|null $fromDate
     * @param string|null $toDate
     * @return mixed
     */
    public function replaceVariableX($mess, $fromDate, $toDate)
    {
        $fromDate = $this->formatDate($fromDate);
        $toDate = $this->formatDate($toDate);
        $preOrderDate = "from " . $fromDate . " to " . $toDate;
        $mess = str_replace(
            ["{preorder_date}"],
            [$preOrderDate],
            $mess
        );
        return $mess;
    }

    /**
     * Check Is Pre Order
     *
     * @param bool $preOrder
     * @param bool $isInStock
     * @param bool $parentStockCheck
     * @param bool $availability
     * @return bool
     */
    public function isPreOrder($preOrder, $isInStock, $availability = true, $parentStockCheck = true)
    {
        if (($preOrder == self::ORDER_YES && $availability) || ($preOrder == self::ORDER_OUT_OF_STOCK && !$isInStock)
            || !$parentStockCheck) {
            return true;
        }

        return false;
    }

    /**
     * Get Availability Message
     *
     * @param mixed $product
     * @return mixed
     */
    public function getAvailabilityMessage($product)
    {
        $message = $product->getData('availability_message');
        $preOrderFromDate = $this->formatDate($product->getData('pre_oder_from_date'));
        $preOrderToDate = $this->formatDate($product->getData('pre_oder_to_date'));
        if ($message) {
            $message = str_replace(
                '{preorder_date}',
                'from ' . $preOrderFromDate . ' to ' . $preOrderToDate,
                $message
            );
        }

        return $message;
    }

    /**
     * @param string $message
     * @param string $preOrderFromDate
     * @param string $preOrderToDate
     * @return mixed
     */
    public function getAvailMessageFromFlatData($message, $preOrderFromDate, $preOrderToDate)
    {
        if ($message) {
            $message = str_replace(
                '{preorder_date}',
                'from ' . $preOrderFromDate . ' to ' . $preOrderToDate,
                $message
            );
        }
        return $message;
    }

    /**
     * Check product is configurable grid view table
     *
     * @return bool
     */
    public function checkProductConfigurableGridView()
    {
        $currentProduct = $this->registry->registry('current_product');
        if ($currentProduct && !$currentProduct->getDisableGridTableView() && $this->isEnabledModuleCPGridView()) {
            return true;
        }
        return false;
    }
    /**
     * Check is enable Module CPGridView
     *
     * @return bool
     */
    public function isEnabledModuleCPGridView()
    {
        $config = $this->scopeConfig->getValue(
            self::ENABLE_MODULE_CONFIGURABLE_GRID_VIEW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $installModule = $this->_moduleManager->isEnabled('Bss_ConfiguableGridView');
        if ($config && $installModule) {
            return true;
        }
        return false;
    }

    /**
     * Get message and add to tooltip
     *
     * @param int $productId
     * @return mixed|string
     */
    public function getAvailabilityMessageByPid($productId)
    {
        $product = $this->productFactory->create()->load($productId);
        if ($product->getSku()) {
            $message = $this->replaceVariableX(
                $product->getMessage(),
                $this->formatDate($product->getPreOderFromDate()),
                $this->formatDate($product->getPreOderToDate())
            );
            if ($message == "") {
                $message = $this->replaceVariableX(
                    $this->getMess(),
                    $this->formatDate($product->getPreOderFromDate()),
                    $this->formatDate($product->getPreOderToDate())
                );
            }
            return $message;
        }
        return '';
    }
}
