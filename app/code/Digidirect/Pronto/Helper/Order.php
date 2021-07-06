<?php

namespace Digidirect\Pronto\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\InventoryApi\Api\SourceItemRepositoryInterface;
use Digidirect\AbstractEntity\Model\AbstractEntityRepository;
use Digidirect\InvoiceIncrementId\Model\IncrementIdUpdater;
use Psr\Log\LoggerInterface;

class Order extends AbstractHelper
{
 
    /**
    * @var Curl
    */
    protected $curl;
    
    protected $_orderCollectionFactory;
    
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    
    /**
     * @var array
     */
    protected $relocateWarehouseMap = [
        'MELB' => 'SWHS',
        'CANN' => 'SWHS',
        'SWHS' => 'MELB'
    ];
    
    /**
     * @var array
     */
    protected $repCodeForPickUp = [
        '11' => 'S7P',
        '13' => 'B4P',
        '17' => 'M1P',
        '21' => 'M6P',
        '19' => 'B5P',
        '23' => 'C3P'
    ];

    /**
     * @var array
     */
    protected $repDispatchWarehouseMap = [
        'MELB' => '85',
        'CANN' => 'C3W',
        'SWHS' => 'C9W'
    ];
    
    /**
     * @var array
     */
    protected $warehouseCode = [];
    
    /**
     * @var SourceItemRepositoryInterface
     */
    protected $sourceItemRepository;
    
    /**
     * @var AbstractEntityRepository
     */
    protected $abstractEntityRepository;
    
    /**
     * @var IncrementIdUpdater
     */
    protected $incrementIdUpdater;
    
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    
    /**
     * @var CustomerInterface[]|array
     */
    protected $customer = [];
    
    /**
     * @var LoggerInterface
     */
    protected $logger;
    
    public function __construct(
                        Curl $curl,
                        JsonSerializer $jsonSerializer,
                        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku,
                        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSaveInterface,
                        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemFactory,
                        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
                        SearchCriteriaBuilder $searchCriteriaBuilder,
                        SourceItemRepositoryInterface $sourceItemRepository,
                        AbstractEntityRepository $abstractEntityRepository,
                        IncrementIdUpdater $incrementIdUpdater,
                        CustomerRepositoryInterface $customerRepository,
                        LoggerInterface $logger)
                    {
                        $this->curl = $curl;
                        $this->jsonSerializer = $jsonSerializer;
                        $this->sourceItemsBySku = $sourceItemsBySku;
                        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
                        $this->sourceItemFactory = $sourceItemFactory;
                        $this->_orderCollectionFactory = $orderCollectionFactory;
                        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
                        $this->sourceItemRepository = $sourceItemRepository;
                        $this->abstractEntityRepository = $abstractEntityRepository;
                        $this->incrementIdUpdater = $incrementIdUpdater;
                        $this->customerRepository = $customerRepository;
                        $this->logger = $logger;

    }

    public function sendOrder() 
    {
 
        $piwikItems = array();
        $piwikOrder = array();
        //get order data
        $orders = $this->getOrderCollection();
        $counter = 0;
        foreach ($orders as $order) {
            $data = array();
            $counter++;
            //var_dump($order);
            /* @var $order \Magento\Sales\Model\Order */
            
            if ($order->getState() == 'canceled') {
                continue;
            }
            
            $orderId = $order->getIncrementId();
            
            //echo "<br />orderId ".$orderId;
            //echo "<br />customerId ".$order->getCustomerId();
            $accountname = $this->getAccountName($order);
            //echo "<br> accountname - ".$accountname. "<br>";
            $contactname = $accountname;
            //check pronto if customer has an account.
            //if not, create customer account to pronto

            $data['sales-order']['header']['accountname'] = $accountname;
            $data['sales-order']['header']['account'] = $this->getAccount($order);
            $data['sales-order']['header']['order-date'] = "";
            $data['sales-order']['header']['warehouse'] = $this->getWarehouse($order);
            $data['sales-order']['header']['territory'] = "WEBS";
            $data['sales-order']['header']['rep'] = $this->getRep($order);
            $data['sales-order']['header']['contactname'] = $contactname;
            $data['sales-order']['header']['email'] = $order->getCustomerEmail();
            $data['sales-order']['header']['reference'] = $orderId;
            $data['sales-order']['header']['set-on-status'] = "I"; //TO DO get status
            
            
            $grandTotal = (double) $order->getBaseGrandTotal();
            $subTotal = (double) $order->getBaseSubtotalInclTax();
            $tax = (double) $order->getBaseTaxAmount();
            $shipping = (double) $order->getBaseShippingInclTax();
            $discount = abs((double) $order->getBaseDiscountAmount());
            
            
            $data['sales-order']['header']['order-total-inc-tax'] = $grandTotal;
            
            $address = $order->getBillingAddress();
            
            
            $strt = $address->getStreet();
            if(is_array($strt))
            {
                $street = implode(",", $strt);
            }
            $postcode = $address->getPostcode();
            $countrycode = $address->getCountryid();
            $phone = $address->getPhone();
            $mobile = $address->getMobile();
            $paymentInstance = $order->getPayment();
            
            $data['sales-order']['header']['billing-address']['line-1'] = $street;
            $data['sales-order']['header']['billing-address']['postcode'] = $postcode;
            $data['sales-order']['header']['billing-address']['country-code'] = $countrycode;
            $data['sales-order']['header']['billing-address']['phone'] = $phone;
            $data['sales-order']['header']['billing-address']['mobile'] = $mobile;
            
            //payment details
            //bank should sync as confirmed by Michael from Emmanuel
//            $is_bank = false;
//            
//            $methodInst = $paymentInstance->getMethodInstance();
//            //echo "<br> payment - ". $paymentInstance->getMethod();
//            $methodTitle = $methodInst->getTitle();
//            //echo "<br >method - ".$methodTitle;
//            
//            if ($paymentInstance->getMethod() == "banktransfer") {
//                unset($data);
//                $is_bank = true;
//            }
//
//            if($is_bank){
//                continue;
//            }
            
            $payment_reference = $paymentInstance->getLastTransId();
            //echo "<br >payment_reference - ".$payment_reference;
            $payment_type = $this->getPaymentType($paymentInstance);
            $amount_tendered = $order->getBaseGrandTotal();
            $amount_tendered = round($amount_tendered, 2);
            
            //echo "<br >amount_tendered - " .$amount_tendered;
            
            
            $data['sales-order']['header']['payment-details']['payment-detail']['payment-type'] = $payment_type;
            $data['sales-order']['header']['payment-details']['payment-detail']['payment-reference'] = $payment_reference;
            $data['sales-order']['header']['payment-details']['payment-detail']['amount-tendered'] = $amount_tendered;
            
            //CUSTOM DATA
            $qffNumber = $order->getQffNumber();
            $qffLastname = $order->getQffLastname();
            if (!empty($qffNumber) && !empty($qffLastname)) {
                $data['sales-order']['header']['custom-data']['data']['key'] = 'QFF';
                $data['sales-order']['header']['custom-data']['data']['value'] = $qffNumber;
                $data['sales-order']['header']['custom-data']['data']['key'] = 'QFFSURNAME';
                $data['sales-order']['header']['custom-data']['data']['value'] = $qffLastname;
            }
            else 
            {
                $data['sales-order']['header']['custom-data']['data']['key'] = 'QFF';
                $data['sales-order']['header']['custom-data']['data']['value'] = NULL;
                $data['sales-order']['header']['custom-data']['data']['key'] = 'QFFSURNAME';
                $data['sales-order']['header']['custom-data']['data']['value'] = NULL;
            }
            
            $data['sales-order']['header']['custom-data']['data']['key'] = 'magento-order-number';
            $data['sales-order']['header']['custom-data']['data']['value'] = $orderId;
            
            //product lines
            $x = 0;
            foreach ($order->getAllVisibleItems() as $item) {
                /* @var $item \Magento\Sales\Model\Order\Item */
                
                $price = (double) $item->getBasePriceInclTax();
                $qty = (double) $item->getQtyOrdered();
                $data['sales-order']['detail']['line'][$x]['line-type'] = 'SN';
                $data['sales-order']['detail']['line'][$x]['stock-code'] = $item->getSku();
                $data['sales-order']['detail']['line'][$x]['description'] = $item->getName();
                $data['sales-order']['detail']['line'][$x]['unit-price-inc-tax'] = $price;
                $data['sales-order']['detail']['line'][$x]['ordered'] = $qty;
                $data['sales-order']['detail']['line'][$x]['shipped'] = $qty;
                $data['sales-order']['detail']['line'][$x]['backordered'] = 0;
                $data['sales-order']['detail']['line'][$x]['sol-disc-rate'] = 0;
                $data['sales-order']['detail']['line'][$x]['sol-line-total-inc-tax'] = $price * $qty;
                $x++;
            }
            
            
            //should be inside the foreach above
            //create xml of order data here
            $xml = \Digidirect\AI\Model\Lib\Adapter\Import\Xml::assocToXml($data, 'sales-orders');

            //echo $xml;

            //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/login';
            //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/sales?call-type=create_orders'; //test
            //live - port :8084
            $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/sales?call-type=create_orders';
            $username = 'clint.mercado';
            $password = '849cd5080faff5ce';
            $jsonData = '{}';

            $this->curl->addHeader("Content-Type", "application/xml");
            $this->curl->addHeader("Accept", "application/json");
            $this->curl->addHeader("compcode", "DIG"); //live
            //$this->curl->addHeader("compcode", "UA1"); //test
            $this->curl->addHeader("user", "clint.mercado");
            $this->curl->addHeader("token", "849cd5080faff5ce");
            $this->curl->post($url, $xml);

            $result = $this->curl->getBody();

            //var_dump($result);
            // echo $result;
            $json = $this->jsonSerializer->unserialize($result);
            //var_dump($json);
            //echo "<br>";
            if(isset($json['response']['status']) && ($json['response']['status'] == 'FAIL'))
            {
                $msg =  $json['response']['message'];
                $this->logger->error('Pronto Order Sync', array('info' => $msg));
                
            }
            else if (isset($json['sales-orders']['response']['status']) && ($json['sales-orders']['response']['status'] == 'failed')) {
                $msg =  $json['sales-orders']['response']['message'];
                $this->logger->error('Pronto Order Sync', array('info' => $msg));
            }
            else {
                //success
                //update order data with pronto order-no below
                //$json['sales-order']['sales-order']['order-no']
                //echo "success";
                //echo "<br>";
                $order->setState("complete")->setStatus("complete");
                $pronto = $json['sales-orders']['sales-order']['order-no'];
                $invoiceno = $json['sales-orders']['sales-order']['invoice-no'];
                $prontostatus = $json['sales-orders']['sales-order']['order-status-code'];
                $order->setData('pronto_order_number',$pronto);
                $order->setData('pronto_status_code',$prontostatus);
                $order->save();
                
                $this->logger->info('Pronto Order Sync', $json['sales-orders']['sales-order']);
                
                $account = $json['sales-orders']['sales-order']['account'];
                if (!empty($account) && !$order->getCustomerIsGuest()) {
                    $customer = $this->customerRepository->getById($order->getCustomerId());
                    $customer->setData('pronto_account_id', $account);
                    $customer->setCustomAttribute('pronto_account_id', $account);
                    $this->customerRepository->save($customer);
                }
                /** @var \Magento\Sales\Model\Order\Invoice $invoice */
                $invoice = $order->getInvoiceCollection()->getFirstItem();
                $this->incrementIdUpdater->update($invoice, $invoiceno);
                //var_dump($json);
                //exit; //for testing;
            }
        }
        
    }   
    
    public function getOrderCollection()
    {
        $now = new \DateTime();
        $collection = $this->_orderCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('pronto_order_number', array('null' => true))
            ->addFieldToFilter('created_at',$now->format('Y-m-d'));
     
     return $collection;
     
    }
    
    public function getPaymentType($paymentInstance){
        
        //echo "<br >get payment type ". $paymentInstance->getMethod();
        $payment = $paymentInstance->getMethod();
        switch ($payment) {
            case "braintree":
              $type = 'BT';
              break;
            case "checkmo":
              $type = 'H';
              break;
            case "free":
              $type = 'VI';
              break;
            case "m2epropayment":
              $type = 'EB';
              break;
          case "braintree_paypal":
              $type = 'PY';
              break;
          case "banktransfer":
              $type = 'Y';
              break;
            default:
              break;
        }
        
        if($type == 'BT')
        {
            $cc = $paymentInstance->getCcType();
            if($cc == 'AE')
            {
                $type = 'X';
            }
        }
        return $type;
    }
    
    public function getWarehouse(OrderInterface $order) {
        if (!isset($this->warehouseCode[$order->getEntityId()])) {
            $whse = '';

            if ($order->getShippingMethod() == 'collect_collect') {
                if ($collectPlaceId = $this->getCollectPlaceId($order)) {
                    $whse = $this->repCodeForPickUp[$collectPlaceId];
                }
            } elseif ($order->getShippingAddress()) {
                $whse = $this->getWarehouseByRegionCode($order->getShippingAddress()->getRegionCode());
                $skus = $this->getProductsSkus($order);
                if (!$this->isProductsInStock($whse, $skus) && isset($this->relocateWarehouseMap[$whse]) && $this->isProductsInStock($this->relocateWarehouseMap[$whse], $skus)) {
                    $whse = $this->relocateWarehouseMap[$whse];
                }
            }

            $this->warehouseCode[$order->getEntityId()] = $whse;
        }
        
        return $this->warehouseCode[$order->getEntityId()];
    }
    
    public function getRep(OrderInterface $order) {
        if ($order->getShippingMethod() == 'collect_collect') {
            if ($collectPlaceId = $this->getCollectPlaceId($order)) {
                $mapping = $this->repCodeForPickUp;
                return $mapping[$collectPlaceId] ?? '';
            }
            return '';
        }
        echo 'Rep - '. $this->repDispatchWarehouseMap[$this->getWarehouse($order)] ?? '';
        return $this->repDispatchWarehouseMap[$this->getWarehouse($order)] ?? '';
    }
    
    protected function getWarehouseByRegionCode($regionCode) {
        return 'SWHS';
    }
    
    /**
     * @param OrderInterface $order
     * @return array
     */
    protected function getProductsSkus(OrderInterface $order) {
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
     * @param string $sourceCode
     * @param array $productsSkus
     * @return bool
     */
    protected function isProductsInStock($sourceCode, array $productsSkus) {
        $sourceItems = $this->getSourceItemBySourceCodeAndSku($sourceCode, $productsSkus);
        foreach ($sourceItems as $sourceItem) {
            if (!$sourceItem->getQuantity() || $sourceItem->getStatus() !== SourceItemInterface::STATUS_IN_STOCK) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * @param OrderItemInterface $item
     * @return array
     */
    protected function getSkusByProductType(OrderItemInterface $item) {
        switch ($item->getProductType()) {
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                return $this->getOrderLinesByBundle($item);
            default:
                return [$item->getSku()];
        }
    }
    
    /**
     * @param string $sourceCode
     * @param string $sku
     * @return SourceItemInterface[]
     */
    protected function getSourceItemBySourceCodeAndSku($sourceCode, array $sku) {
        $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(SourceItemInterface::SOURCE_CODE, $sourceCode)
                ->addFilter(SourceItemInterface::SKU, $sku, 'in')
                ->create();
        $sourceItemsResult = $this->sourceItemRepository->getList($searchCriteria);
        return $sourceItemsResult->getItems();
    }
    
    /**
     * @param OrderInterface $order
     * @return int|null
     */
    protected function getCollectPlaceId(OrderInterface $order) {
        foreach ($order->getAllVisibleItems() as $item) {
            if ($collectPlaceId = $item->getCollectPlaceId()) {
                return $collectPlaceId;
            }
        }
        return null;
    }
    
    /**
     * @param OrderInterface $order
     * @param string $attributeCode
     * @return mixed|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCustomerAttributeValue(OrderInterface $order, $attributeCode) {
        $result = '';
        if (!$order->getCustomerIsGuest() && !isset($this->customer[$order->getEntityId()])) {
            //echo "<br/>not guest";
            try {
                $this->customer[$order->getEntityId()] = $this->customerRepository->getById($order->getCustomerId());
            } catch (Exception $ex) {
                return $result;
            }
            
        }

        if (isset($this->customer[$order->getEntityId()])) {
            //echo "<br />get entity";
            $customer = $this->customer[$order->getEntityId()];
            $attribute = $customer->getCustomAttribute($attributeCode);
            $result = $attribute ? $attribute->getValue() : '';
        }

        return $result;
    }
    
    /**
     * @param OrderInterface|Order $order
     * @return string
     */
    public function getAccountName(OrderInterface $order) {
        //echo "<br /> get accountname. ";
        $accountName = $this->getCustomerAttributeValue($order, 'pronto_account_name');
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
    public function getAccount(OrderInterface $order) {
        return $this->getCustomerAttributeValue($order, 'pronto_account_id');
    }
    
}      
