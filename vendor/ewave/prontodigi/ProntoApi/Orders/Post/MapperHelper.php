<?php

namespace Ewave\ProntoDigi\ProntoApi\Orders\Post;

use Ewave\AbstractEntity\Model\AbstractEntityRepository;
use Ewave\CheckoutFields\Helper\Data as CheckoutFieldsDataHelper;
use Ewave\Collect\Model\Carrier\Collectcarrier;
use Ewave\ProntoDigi\ProntoApi\Constants\CustomerAttributes;
use Ewave\ProntoDigi\ProntoApi\Constants\Order\OrderLine as OLConst;
use Magento\Braintree\Model\Ui\ConfigProvider as BraintreeConfigProvider;
use Magento\Braintree\Model\Ui\PayPal\ConfigProvider as BraintreePaypalConfigProvider;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Magento\OfflinePayments\Model\Banktransfer;
use Magento\Paypal\Model\Config as PaypalConfig;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use ZipMoney\ZipMoneyPayment\Model\Config as ZipPayConfig;

class MapperHelper
{
    /**
     * @var CheckoutFieldsDataHelper
     */
    protected $checkoutFieldsDataHelper;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SourceItemRepositoryInterface
     */
    protected $sourceItemRepository;

    /**
     * @var CustomerInterface|null
     */
    protected $customer;

    /**
     * @var AbstractEntityRepository
     */
    protected $abstractEntityRepository;

    /**
     * @var array
     */
    protected $regionWarehouseMap = [
        'SWHS' => ['NSW', 'ACT'],
        'MELB' => ['VIC', 'TAS', 'NT', 'SA', 'QLD'],
        'CANN' => ['WA'],
    ];

    /**
     * @var array
     */
    protected $relocateWarehouseMap = ['MELB' => 'SWHS', 'CANN' => 'SWHS', 'SWHS' => 'MELB'];

    /**
     * @var array
     */
    protected $repDispatchWarehouseMap = ['MELB' => '85', 'CANN' => 'C3W', 'SWHS' => 'C9W'];

    /**
     * @var array
     */
    protected $repStorePickUpWarehouseMap = [
        'BRIS' => 'B5W',
        'MIRA' => 'M6W',
        'SYDN' => 'S7W',
        'MELB' => 'M1P',
        'CANN' => 'C3P',
        'BOND' => 'B4P'
    ];

    /**
     * @var array
     */
    protected $warehouseCode = [];

    /**
     * MapperHelper constructor.
     * @param CheckoutFieldsDataHelper $checkoutFieldsDataHelper
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SourceItemRepositoryInterface $sourceItemRepository
     * @param AbstractEntityRepository $abstractEntityRepository
     */
    public function __construct(
        CheckoutFieldsDataHelper $checkoutFieldsDataHelper,
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SourceItemRepositoryInterface $sourceItemRepository,
        AbstractEntityRepository $abstractEntityRepository
    ) {
        $this->checkoutFieldsDataHelper = $checkoutFieldsDataHelper;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceItemRepository = $sourceItemRepository;
        $this->abstractEntityRepository = $abstractEntityRepository;
    }

    /**
     * @param OrderInterface|Order $order
     * @return string
     */
    public function getAccountName(OrderInterface $order)
    {
        $accountName = $this->getCustomerAttributeValue($order, CustomerAttributes::PRONTO_ACCOUNT_NAME);
        if (empty($accountName)) {
            $address = $order->getShippingAddress() ?? $order->getBillingAddress();
            $accountName = $address->getName();
        }
        return $accountName;
    }

    /**
     * @param OrderInterface|Order $order
     * @return string
     */
    public function getAccount(OrderInterface $order)
    {
        return $this->getCustomerAttributeValue($order, CustomerAttributes::PRONTO_ACCOUNT_ID);
    }

    /**
     * @param OrderInterface $order
     * @return float
     */
    public function getAmountTendered(OrderInterface $order)
    {
        $value = (float)$order->getBaseGiftCardsAmount();
        return round($value, 2);
    }
    
    /**
     * @param OrderInterface $order
     * @return string
     */
    public function getRep(OrderInterface $order)
    {
        if ($order->getShippingMethod() == Collectcarrier::COLLECT_SHIPPING_METHOD) {
            return $this->repStorePickUpWarehouseMap[$this->getWarehouse($order)] ?? '';
        } else {
            return $this->repDispatchWarehouseMap[$this->getWarehouse($order)] ?? '';
        }
    }

    /**
     * @param OrderInterface|Order $order
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWarehouse(OrderInterface $order)
    {
        if (!isset($this->warehouseCode[$order->getEntityId()])) {
            $whse = '';

            if ($order->getShippingMethod() == Collectcarrier::COLLECT_SHIPPING_METHOD) {
                $collectPlaceId = null;
                foreach ($order->getAllVisibleItems() as $item) {
                    if ($item->getCollectPlaceId()) {
                        $collectPlaceId = $item->getCollectPlaceId();
                        break;
                    }
                }
                if ($collectPlaceId) {
                    $whse = $this->abstractEntityRepository->getById($collectPlaceId)->getCode();
                }
            } elseif ($order->getShippingAddress()) {
                $whse = $this->getWarehouseByRegionCode($order->getShippingAddress()->getRegionCode());
                $skus = $this->getProductsSkus($order);
                if (!$this->isProductsInStock($whse, $skus) && isset($this->relocateWarehouseMap[$whse])
                    && $this->isProductsInStock($this->relocateWarehouseMap[$whse], $skus)) {
                    $whse = $this->relocateWarehouseMap[$whse];
                }
            }

            $this->warehouseCode[$order->getEntityId()] = $whse;
        }
        return $this->warehouseCode[$order->getEntityId()];
    }

    /**
     * @param OrderInterface|Order $order
     * @return string
     */
    public function getContactName(OrderInterface $order)
    {
        $name = $order->getCustomerName();
        if ($order->getCustomerIsGuest()) {
            $address = $order->getShippingAddress() ?? $order->getBillingAddress();
            $name = $address->getName();
        }
        return $name;
    }

    /**
     * @param OrderInterface|Order $order
     * @return string
     */
    public function getPurchaseOrderNumber(OrderInterface $order)
    {
        return (string)$this->checkoutFieldsDataHelper->getCustomCheckoutOrderFieldValue($order, 'delivery_number');
    }

    /**
     * @param OrderInterface|Order $order
     * @param int $start
     * @param int|null $length
     * @return string
     */
    public function getOrderComment(OrderInterface $order, $start, $length = null)
    {
        $data = (string)$this->checkoutFieldsDataHelper->getCustomCheckoutOrderFieldValue($order, 'order_comment');
        return $length ? substr($data, $start, $length) : substr($data, $start);
    }

    /**
     * @param OrderInterface|Order $order
     * @param int $start
     * @param int|null $length
     * @return string
     */
    public function getDeliveryNotes(OrderInterface $order, $start, $length = null)
    {
        $data = (string)$this->checkoutFieldsDataHelper->getCustomCheckoutOrderFieldValue($order, 'delivery_notes');
        return $length ? substr($data, $start, $length) : substr($data, $start);
    }

    /**
     * @param OrderInterface $order
     * @return string
     * @throws \Exception
     */
    public function getPaymentType(OrderInterface $order)
    {
        $payment = $order->getPayment();
        /** @var \Magento\Payment\Model\Method\Adapter $method */
        $method = $payment->getMethodInstance();

        switch ($method->getCode()) {
            case BraintreeConfigProvider::CODE:
                return 'BT';
            case PaypalConfig::METHOD_EXPRESS:
            case BraintreePaypalConfigProvider::PAYPAL_CODE:
                return 'PY';
            case ZipPayConfig::METHOD_CODE:
                return 'ZM';
            case Banktransfer::PAYMENT_METHOD_BANKTRANSFER_CODE:
                return 'Y';
            case 'free':
                return 'VI';
        }
        throw new \Exception('Could not define payment type');
    }

    /**
     * @param OrderInterface|Order $order
     * @return array
     */
    public function getOrderLines(OrderInterface $order)
    {
        $orderLines = [];

        /** @var $item \Magento\Sales\Model\Order\Item */
        foreach ($order->getAllVisibleItems() as $item) {
            $itemLines = $this->getOrderLinesByOrderItem($item);
            $orderLines = array_merge($orderLines, $itemLines);
        }

        if ((float)$order->getBaseShippingAmount()) {
            $orderLines = array_merge($orderLines, $this->getShippingLine($order));
        }
        return $orderLines;
    }

    /**
     * @param OrderInterface|Order $order
     * @return float
     */
    public function getOrderTotalIncTax(OrderInterface $order)
    {
        return $order->getBaseGrandTotal();
    }

    /**
     * @param \Magento\Sales\Model\Order\Item $item
     * @return array
     */
    protected function getOrderLinesByOrderItem($item)
    {
        switch ($item->getProductType()) {
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                return $this->getOrderLinesByBundle($item);
            default:
                return $this->getOrderLine($item);
        }
    }

    /**
     * @param OrderItemInterface|\Magento\Sales\Model\Order\Item $item
     * @return array
     */
    protected function getOrderLinesByBundle(OrderItemInterface $item)
    {
        $orderLines = [];
        $children = $item->getChildrenItems();
        foreach ($children as $child) {
            $orderLines = array_merge($orderLines, $this->getOrderLinesByOrderItem($child));
        }

        return $orderLines;
    }

    /**
     * @param OrderItemInterface $item
     * @return array
     */
    protected function getOrderLine(OrderItemInterface $item)
    {
        $orderLines = [];
        $discount = abs($item->getDiscountAmount());
        $lineType = 'SN';
        if ($item->getProductType() == ProductType::TYPE_VIRTUAL) {
            $lineType = 'SS';
        }
        $orderLines[] = [
            OLConst::LINE_TYPE => $lineType,
            OLConst::STOCK_CODE => $item->getSku(),
            OLConst::UNIT_PRICE_INC_TAX => $item->getBasePriceInclTax() - ($discount / $item->getQtyOrdered()),
            OLConst::ORDERED => $item->getQtyOrdered(),
            OLConst::BACKORDERED => $item->getQtyOrdered(),
            OLConst::SHIPPED => 0,
            OLConst::SOL_DISC_RATE => 0,
            OLConst::SOL_LINE_TOTAL_INC_TAX => $item->getBaseRowTotalInclTax() - $discount,
        ];
        return $orderLines;
    }

    /**
     * @param OrderInterface $item
     * @return array
     */
    protected function getShippingLine(OrderInterface $order)
    {
        $orderLines[] = [
            OLConst::LINE_TYPE => 'SC',
            OLConst::STOCK_CODE => '',
            OLConst::UNIT_PRICE_INC_TAX => $order->getBaseShippingAmount(),
            OLConst::ORDERED => 1,
            OLConst::BACKORDERED => 1,
            OLConst::SHIPPED => 0,
            OLConst::SOL_DISC_RATE => 0,
            OLConst::SOL_CHG_TYPE => 0,
            OLConst::SOL_LINE_TOTAL_INC_TAX => $order->getBaseShippingAmount(),
        ];
        return $orderLines;
    }

    /**
     * @param OrderInterface $order
     * @param string $attributeCode
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCustomerAttributeValue(OrderInterface $order, $attributeCode)
    {
        $result = '';
        if (!$order->getCustomerIsGuest() && !$this->customer) {
            $this->customer = $this->customerRepository->getById($order->getCustomerId());
        }

        if ($this->customer) {
            $attribute = $this->customer->getCustomAttribute($attributeCode);
            $result = $attribute ? $attribute->getValue() : '';
        }

        return $result;
    }

    /**
     * @param string $regionCode
     * @return string
     */
    protected function getWarehouseByRegionCode($regionCode)
    {
        $warehouse = '';
        foreach ($this->regionWarehouseMap as $whsCode => $regions) {
            if (in_array($regionCode, $regions)) {
                $warehouse = $whsCode;
                break;
            }
        }
        return $warehouse;
    }

    /**
     * @param string $sourceCode
     * @param array $productsSkus
     * @return bool
     */
    protected function isProductsInStock($sourceCode, array $productsSkus)
    {
        $sourceItems = $this->getSourceItemBySourceCodeAndSku($sourceCode, $productsSkus);
        foreach ($sourceItems as $sourceItem) {
            if (!$sourceItem->getQuantity() || $sourceItem->getStatus() !== SourceItemInterface::STATUS_IN_STOCK) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $sourceCode
     * @param string $sku
     * @return SourceItemInterface[]
     */
    protected function getSourceItemBySourceCodeAndSku($sourceCode, array $sku)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(SourceItemInterface::SOURCE_CODE, $sourceCode)
            ->addFilter(SourceItemInterface::SKU, $sku, 'in')
            ->create();
        $sourceItemsResult = $this->sourceItemRepository->getList($searchCriteria);
        return $sourceItemsResult->getItems();
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    protected function getProductsSkus(OrderInterface $order)
    {
        $skus = [];
        /** @var $item \Magento\Sales\Model\Order\Item */
        foreach ($order->getAllVisibleItems() as $item) {
            if (!$item->getIsVirtual()) {
                $skus = array_merge($skus, $this->getSkusByProductType($item));
            }
        }

        return $skus;
    }

    /**
     * @param OrderItemInterface $item
     * @return array
     */
    protected function getSkusByProductType(OrderItemInterface $item)
    {
        switch ($item->getProductType()) {
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                return $this->getOrderLinesByBundle($item);
            default:
                return [$item->getSku()];
        }
    }

    /**
     * @param OrderItemInterface $item
     * @return array
     */
    protected function getSkusByBundle(OrderItemInterface $item)
    {
        /**
         * @var $item \Magento\Sales\Model\Order\Item
         */
        $skus = [];
        $children = $item->getChildrenItems();
        foreach ($children as $child) {
            $skus = array_merge($skus, $this->getSkusByProductType($child));
        }

        return $skus;
    }
}
