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
use Magento\Directory\Model\Country;
use Magento\Directory\Model\CountryFactory;

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
        '17' => 'M1P',
        '21' => 'M6P',
        '23' => 'C3P',
        '1'  => 'S7P',
        '4'  => 'B4P',
        '7'  => 'M1P',
        '10' => 'B5P',
        '13' => 'M6P',
        '16' => 'C3P',
        '19' => 'B5P'

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

    protected $logger;

    private $timezone;

    /**
     * @var Country
     */
    public $countryFactory;

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
                        \Digidirect\CustomOrderLog\Logger\Logger $logger,
                        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
                        CountryFactory $countryFactory)
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
                        $this->timezone = $timezone;
                        $this->countryFactory = $countryFactory;

    }

    public function orderPost()
    {

        //get order data
        $orders = $this->getOrderCollection();
        $counter = 0;
        foreach ($orders as $order)
        {
            $data = array();
            $counter++;

            /* @var $order \Magento\Sales\Model\Order */

            if ($order->getState() == 'canceled') {
                continue;
            }

            $orderId = $order->getIncrementId();
            $entityId = $order->getId();
            $this->logger->info('Pronto Order Sync - '.$orderId);

            //Amazon Logic
            $wrehs = $this->getWarehouse($order);
            $territory = "WEBS";
            if($wrehs != 'SWHS')
            {
                $territory = $wrehs;
            }
            $accountname = $this->getAccountName($order);
            $account = $this->getAccount($order);
            $address = $order->getBillingAddress();
            $countrycode = $address->getCountryId();
            $countryName = "";
            if(isset($countrycode))
            {
                $country = $this->countryFactory->create()->loadByCode($countrycode);
                if ($country) {
                    $countryName = $country->getName();
                }
            }


            $amShipping = $order->getShippingDescription();
            $is_am_order = false;
            $is_am_fba = false;
            if (strpos($orderId, 'AM') !== false) {
                $is_am_order = true;
            }

            if($is_am_order){
                $rep = "AMAZON MFN";
                if (strpos($amShipping, 'AFN') !== false) {
                    $rep = "AMAZON FBA";
                    $account = "AMAZ00";
                    if($countrycode == "NZ")
                    {
                        $account = "AMAZ01";
                    }

                    $wrehs = "AWHS";
                    $territory = "AWHS";
                    $is_am_fba = true;
                }

            }
            else
            {
                $rep = $this->getRep($order);
                if (strpos($orderId, 'EB') !== false) {
                    $rep ="EBAY";
                }
                else if (strpos($orderId, 'CATCH') !== false) {
                    $rep ="CATCH";
                }
                else if (strpos($orderId, 'MYD') !== false) {
                    $rep ="MYDEAL";
                }
                else if (strpos($orderId, 'WD') !== false) {
                    $rep ="WESTFIELD";
                    $account = "WEST00";
                    //for westfield
                }

            }

            $contactname = $accountname;
            //check pronto if customer has an account.
            //if not, create customer account to pronto

            //fixed date to use store timezone
            $created = $order->getCreatedAt();
            $created = $this->timezone->date(new \DateTime($created));
            $orderdate = $created->format('Y-m-d');

            $customerEmail = $order->getCustomerEmail();
            $data['sales-order']['header']['accountname'] = $accountname;
            $data['sales-order']['header']['account'] = $account;
            $data['sales-order']['header']['order-date'] = $orderdate;
            $data['sales-order']['header']['warehouse'] = $wrehs;
            $data['sales-order']['header']['customer-type'] = "WC";
            $data['sales-order']['header']['so-cust-type'] = "WC";
            $data['sales-order']['header']['territory'] = $territory;
            $data['sales-order']['header']['rep'] = $rep;
            $data['sales-order']['header']['contactname'] = $contactname;
            $data['sales-order']['header']['email'] = $customerEmail;
            $data['sales-order']['header']['reference'] = $entityId;
            $data['sales-order']['header']['on-hold-reason-code'] = "01";
            $data['sales-order']['header']['set-on-status'] = "H";
            $data['sales-order']['header']['so-part-shipment-allowed'] = "N";

            //echo "<br> WH - ".$data['sales-order']['header']['warehouse'];
            $grandTotal = (double) $order->getBaseGrandTotal();
            $subTotal = (double) $order->getBaseSubtotalInclTax();
            $tax = (double) $order->getBaseTaxAmount();
            $shipping = (double) $order->getBaseShippingInclTax();

            $data['sales-order']['header']['order-total-inc-tax'] = $grandTotal;

            $strt = $address->getStreet();
            if(is_array($strt))
            {
                $street = implode(",", $strt);
            }
            $city = $address->getCity();
            $region = $address->getRegion();
            $postcode = $address->getPostcode();
            $countrycode = $address->getCountryId();
            $phone = $address->getTelephone();
            $mobile = $address->getMobile();
            $company = $address->getCompany();
            $unitNumber = $address->getUnitNumber();
            if(!empty($unitNumber))
            {
                $unitNumber = str_replace("unit_number"," ",$unitNumber);
            }

            $data['sales-order']['header']['billing-address']['line-1'] = $company;
            $data['sales-order']['header']['billing-address']['line-2'] = $unitNumber." ".$street;
            $data['sales-order']['header']['billing-address']['line-3'] = $city;
            $data['sales-order']['header']['billing-address']['line-4'] = $region;
            $data['sales-order']['header']['billing-address']['line-6'] = $countryName;
            $data['sales-order']['header']['billing-address']['postcode'] = $postcode;
            $data['sales-order']['header']['billing-address']['country-code'] = $countrycode;
            $data['sales-order']['header']['billing-address']['phone'] = $phone;
            $data['sales-order']['header']['billing-address']['mobile'] = $mobile;

            $delivery = $order->getShippingDescription();

            $shipaddress = $order->getShippingAddress();
            $shipstrt = $shipaddress->getStreet();
            if(is_array($shipstrt))
            {
                $shipstreet = implode(",", $shipstrt);
            }
            $shipcity = $shipaddress->getCity();
            $shipregion = $shipaddress->getRegion();
            $shippostcode = $shipaddress->getPostcode();
            $shipcountrycode = $shipaddress->getCountryId();
            $shipphone = $shipaddress->getTelephone();
            $shipmobile = $shipaddress->getMobile();
            $shipcompany = $shipaddress->getCompany();
            $shipUnitNumber = $shipaddress->getUnitNumber();
            if(!empty($shipUnitNumber))
            {
                $shipUnitNumber = str_replace("unit_number"," ",$shipUnitNumber);
            }

            if($delivery == "Pick Up in Store - Click and Collect Shipping")
            {
                $shipcompany = 'Click and Collect';
            }
            else if($rep == "WESTFIELD")
            {
                $shipcompany = 'Click and Collect';
            }

            $data['sales-order']['header']['delivery-address']['line-1'] = $contactname;
            $data['sales-order']['header']['delivery-address']['line-2'] = $shipcompany;
            $data['sales-order']['header']['delivery-address']['line-3'] = $shipUnitNumber." ".$shipstreet;
            $data['sales-order']['header']['delivery-address']['line-4'] = $shipcity;
            $data['sales-order']['header']['delivery-address']['line-5'] = $shipregion;
            $data['sales-order']['header']['delivery-address']['line-6'] = $countryName;
            $data['sales-order']['header']['delivery-address']['postcode'] = $shippostcode;
            $data['sales-order']['header']['delivery-address']['country-code'] = $shipcountrycode;
            $data['sales-order']['header']['delivery-address']['phone'] = $shipphone;
            $data['sales-order']['header']['delivery-address']['mobile'] = $shipmobile;

            $paymentInstance = $order->getPayment();

            //payment details

            //$methodInst = $paymentInstance->getMethodInstance();
            $method = $paymentInstance->getMethod();

            $payment_type = $this->getPaymentType($paymentInstance);
            $cc = "";
            if($payment_type == 'BT')
            {
                $cc = $paymentInstance->getCcType();
            }

            $payment_reference = $paymentInstance->getLastTransId();

//            if (empty($payment_reference) && ($method == 'm2epropayment')) {
//                $payment_reference = $paymentInstance->getAdditionalInformation('channel_order_id');
//            }
            //ebay
            if (($method == 'm2epropayment')) {
                if($paymentInstance->getAdditionalInformation('component_mode') == 'ebay')
                {
                    $payment_reference = $paymentInstance->getAdditionalInformation('channel_order_id');
                }
                else if (empty($payment_reference))
                {
                    $payment_reference = $paymentInstance->getAdditionalInformation('channel_order_id');
                }
            }

            //work around for new and old catch
            if($payment_type == 'H')
            {
                if (strpos($orderId, 'CATCH') !== false) {
                    $payment_type ="CA";
                    $catchRef = $orderId;
                    $catchRef = str_replace("CATCH","",$catchRef);
                    $payment_reference = $catchRef;
                }
                else if (strpos($orderId, 'MYD') !== false) {
                    $payment_type ="MD";
                    $catchRef = $orderId;
                    $catchRef = str_replace("MYD","",$catchRef);
                    $payment_reference = $catchRef;
                }
                else if (strpos($orderId, 'AM') !== false) {
                    $payment_type ="AM";
                    $catchRef = $orderId;
                    $catchRef = str_replace("AM","",$catchRef);
                    $payment_reference = $catchRef;
                }
                else if (strpos($orderId, 'WD') !== false) {
                    $payment_type ="WD";
                    $catchRef = $orderId;
                    $catchRef = str_replace("WD","",$catchRef);
                    $payment_reference = $catchRef;
                }
                else if (strpos($orderId, 'EB') !== false) {
                    $payment_type ="EB";
                    $catchRef = $orderId;
                    $catchRef = str_replace("EB","",$catchRef);
                    $payment_reference = $catchRef;
                }
            }

            if(($is_am_order) && ($payment_type == "EB")){
                $payment_type = "AM";
            }

            $withpaymentref = true;
            if(($payment_type == "Y"))
            {
                $withpaymentref = false;
            }
            if(($payment_type == "H"))
            {
                $withpaymentref = false;
            }
            if(($payment_type == "VI"))
            {
                $withpaymentref = false;
            }
            //gift cards
            $withGC = false;
            $gift_amount = $order->getGiftCardsAmount();
            if($gift_amount > 0)
            {
                $withGC = true;
                $gift_amount = round($gift_amount, 2);
                $gc_reference = $order->getGiftCards('c');
                $data['sales-order']['header']['payment-details']['payment-detail'][0]['payment-type'] = "VI";
                $data['sales-order']['header']['payment-details']['payment-detail'][0]['payment-reference'] = $gc_reference;
                $data['sales-order']['header']['payment-details']['payment-detail'][0]['amount-tendered'] = $gift_amount;
            }

            $amount_tendered = $order->getBaseGrandTotal();
            $amount_tendered = round($amount_tendered, 2);
            if((!$is_am_fba))
            {
                if($withpaymentref)
                {
                    if($withGC)
                    {
                        $data['sales-order']['header']['payment-details']['payment-detail'][1]['payment-type'] = $payment_type;
                        $data['sales-order']['header']['payment-details']['payment-detail'][1]['payment-reference'] = $payment_reference." ".$cc;
                        $data['sales-order']['header']['payment-details']['payment-detail'][1]['amount-tendered'] = $amount_tendered;
                    }
                    else
                    {
                        $data['sales-order']['header']['payment-details']['payment-detail']['payment-type'] = $payment_type;
                        $data['sales-order']['header']['payment-details']['payment-detail']['payment-reference'] = $payment_reference." ".$cc;
                        $data['sales-order']['header']['payment-details']['payment-detail']['amount-tendered'] = $amount_tendered;
                    }

                }

            }

            //CUSTOM DATA
            $qffNumber = $order->getQffNumber();
            $qffLastname = $order->getQffLastname();
            if (!empty($qffNumber) && !empty($qffLastname)) {
                $data['sales-order']['header']['custom-data']['data'][0]['key'] = 'QFF';
                $data['sales-order']['header']['custom-data']['data'][0]['value'] = $qffNumber;
                $data['sales-order']['header']['custom-data']['data'][1]['key'] = 'QFFSURNAME';
                $data['sales-order']['header']['custom-data']['data'][1]['value'] = $qffLastname;
            }
            else
            {
                $data['sales-order']['header']['custom-data']['data'][0]['key'] = 'QFF';
                $data['sales-order']['header']['custom-data']['data'][0]['value'] = NULL;
                $data['sales-order']['header']['custom-data']['data'][1]['key'] = 'QFFSURNAME';
                $data['sales-order']['header']['custom-data']['data'][1]['value'] = NULL;
            }

            $data['sales-order']['header']['custom-data']['data'][2]['key'] = 'magento-order-number';
            $data['sales-order']['header']['custom-data']['data'][2]['value'] = $orderId;

            $data['sales-order']['header']['custom-data']['data'][3]['key'] = 'email';
            $data['sales-order']['header']['custom-data']['data'][3]['value'] = $customerEmail;

            //for coupon
            $coupon = $order->getCouponCode();
            $couponDiscount = ((double) $order->getBaseDiscountAmount());

            //product lines
            $x = 0;
            foreach ($order->getAllVisibleItems() as $item) {
                /* @var $item \Magento\Sales\Model\Order\Item */

                $skus = array();
                $productSku = "";
                $digiProtect = "";
                $price = (double) $item->getBasePriceInclTax();
                $qty = (double) $item->getQtyOrdered();
                $discount = (double) $item->getDiscountAmount();
                $total = ($price * $qty) - $discount;
                if($coupon != "")
                {
                    $discount = 0;
                }
                $digiProtectPrice = 0;
                $digiProtectQty = 0;
                $digiProtectdiscount = 0;
                $digiProtectTotal = 0;

                $sku = $item->getSku();
                if(strpos($sku, '-') !== false)
                {
                    $skus = explode('-', $sku);
                    $productSku = $skus[0];
                    $digiProtect = $skus[1];

                    $price = (double) $item->getBasePriceInclTax();
                    $orig = (double) $item->getOriginalPrice();
                    $digiProtectPrice = $price - $orig;
                    $digiProtectQty = (double) $item->getQtyOrdered();
                    $digiProtectdiscount = (double) $item->getDiscountAmount();
                    if($coupon != "")
                    {
                        $digiProtectdiscount = 0;
                    }
                    $digiProtectTotal = ($digiProtectPrice * $digiProtectQty) - $digiProtectdiscount;
                    $price = $orig;

                }
                else
                {
                    $productSku = $sku;

                }


                $data['sales-order']['detail']['line'][$x]['line-type'] = 'SN';
                $data['sales-order']['detail']['line'][$x]['stock-code'] = $productSku;
                $data['sales-order']['detail']['line'][$x]['description'] = $item->getName();
                $data['sales-order']['detail']['line'][$x]['unit-price-inc-tax'] = $price;
                $data['sales-order']['detail']['line'][$x]['ordered'] = $qty;
                $data['sales-order']['detail']['line'][$x]['shipped'] = 0;
                $data['sales-order']['detail']['line'][$x]['backordered'] = $qty;
                $data['sales-order']['detail']['line'][$x]['sol-disc-rate'] = $discount;
                $data['sales-order']['detail']['line'][$x]['sol-line-total-inc-tax'] = $total;
                $x++;

                if(!empty($digiProtect))
                {
                    $price = (double) $item->getBasePriceInclTax();
                    $qty = (double) $item->getQtyOrdered();
                    $discount = (double) $item->getDiscountAmount();
                    $total = ($price * $qty) - $discount;
                    $data['sales-order']['detail']['line'][$x]['line-type'] = 'SN';
                    $data['sales-order']['detail']['line'][$x]['stock-code'] = $digiProtect;
                    $data['sales-order']['detail']['line'][$x]['description'] = "digiProtect";
                    $data['sales-order']['detail']['line'][$x]['unit-price-inc-tax'] = $digiProtectPrice;
                    $data['sales-order']['detail']['line'][$x]['ordered'] = $digiProtectQty;
                    $data['sales-order']['detail']['line'][$x]['shipped'] = 0;
                    $data['sales-order']['detail']['line'][$x]['backordered'] = $digiProtectQty;
                    $data['sales-order']['detail']['line'][$x]['sol-disc-rate'] = $digiProtectdiscount;
                    $data['sales-order']['detail']['line'][$x]['sol-line-total-inc-tax'] = $digiProtectTotal;
                    $x++;
                }
            }


            if($coupon != "")
            {
                $data['sales-order']['detail']['line'][$x]['line-type'] = 'SC';
                $data['sales-order']['detail']['line'][$x]['description'] = $coupon;
                $data['sales-order']['detail']['line'][$x]['unit-price-inc-tax'] = $couponDiscount;
                $data['sales-order']['detail']['line'][$x]['ordered'] = 1;
                $data['sales-order']['detail']['line'][$x]['shipped'] = 1;
                $data['sales-order']['detail']['line'][$x]['sol-disc-rate'] = 0;
                $data['sales-order']['detail']['line'][$x]['sol-chg-type'] = 5;
                $data['sales-order']['detail']['line'][$x]['sol-line-total-inc-tax'] = $couponDiscount;
                $x++; // for shipping counter
            }

            $shippingprice = (double) $order->getShippingAmount();
            $shippingDesc = $order->getShippingDescription();
            if (strpos($shippingDesc, '|') !== false) {
                $marketplacesShipping = explode('|', $shippingDesc);
                $shippingDesc = $marketplacesShipping[1];
            }
            if($shippingDesc == "Express - (1 to 3 Days)")
            {
                $shippingDesc = "Australia Post – express";
            }
            else if($shippingDesc == "Standard - (4 to 7 Days)")
            {
                $shippingDesc = "Australia Post – eParcel";
            }
            else if($rep == 'WESTFIELD')
            {
                $shippingDesc = "Click and Collect";
            }
            else if($shippingDesc == "AU_ExpressPostParcelSignature")
            {
                $shippingDesc = "Australia Post – express";
            }
            else if($shippingDesc == "AU_RegularParcelWithTrackingAndSignature")
            {
                $shippingDesc = "Australia Post – eParcel";
            }

            //shipping details
            $data['sales-order']['detail']['line'][$x]['line-type'] = 'SC';
            $data['sales-order']['detail']['line'][$x]['description'] = $shippingDesc;
            $data['sales-order']['detail']['line'][$x]['unit-price-inc-tax'] = $shippingprice;
            $data['sales-order']['detail']['line'][$x]['ordered'] = 1;
            $data['sales-order']['detail']['line'][$x]['shipped'] = 1;
            $data['sales-order']['detail']['line'][$x]['sol-disc-rate'] = 0;
            $data['sales-order']['detail']['line'][$x]['sol-chg-type'] = 0;
            $data['sales-order']['detail']['line'][$x]['sol-line-total-inc-tax'] = $shippingprice;

            //create xml of order data here
            $this->logger->info('Pronto Order Sync Data - ',$data['sales-order']);
            $xml = \Digidirect\AI\Model\Lib\Adapter\Import\Xml::assocToXml($data, 'sales-orders');

            //TEST
            //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/sales?call-type=create_orders'; //TEST

            //LIVE - port :8084
            $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/sales?call-type=create_orders';


            $islive = true;
            if($islive)
            {
                $this->curl->addHeader("Content-Type", "application/xml");
                $this->curl->addHeader("Accept", "application/json");
                $this->curl->addHeader("compcode", "DIG"); //live
                $this->curl->addHeader("user", "ewaveapi");
                $this->curl->addHeader("token", "904241bdbf10efa9");
                //
                //$this->curl->addHeader("compcode", "UA1"); //test
                //$this->curl->addHeader("user", "clint.mercado");
                //$this->curl->addHeader("token", "849cd5080faff5ce");

                $this->curl->post($url, $xml);

                $result = $this->curl->getBody();

                $json = $this->jsonSerializer->unserialize($result);

                if(isset($json['response']['status']) && ($json['response']['status'] == 'FAIL'))
                {
                    $msg =  $json['response']['message'];
                    //echo $msg."<br>";
                    $order->setData('pronto_order_number',$msg);
                    $order->save();
                    $this->logger->error('Pronto Order Sync', array('info' => $msg));

                }
                else if (isset($json['sales-orders']['response']['status']) && ($json['sales-orders']['response']['status'] == 'failed')) {
                    $msg =  $json['sales-orders']['response']['message'];
                    //echo $msg ."<br>";
                    $order->setData('pronto_order_number',$msg);
                    $order->save();
                    $this->logger->error('Pronto Order Sync', array('info' => $msg));

                }
                else {

                    $pronto = $json['sales-orders']['sales-order']['order-no'];
                    $invoiceno = $json['sales-orders']['sales-order']['invoice-no'];
                    $prontostatus = $json['sales-orders']['sales-order']['order-status-code'];
                    $order->setData('pronto_order_number',$pronto);
                    $order->setData('pronto_status_code',$prontostatus);
                    $order->save();


                    $this->logger->info('Pronto Order Sync ', $json['sales-orders']['sales-order']);

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

                }
            }

            if($counter >= 2)
            {
                return true; //return after 2 orders
            }

        }
        return true;
    }

    public function getOrderCollection()
    {

        $collection = $this->_orderCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('pronto_order_number', array('null' => true))
            ->addFieldToFilter('status',array('neq' => 'canceled'))
            ->addFieldToFilter('entity_id', array('gteq' => 615813))
            ->setOrder('created_at', 'asc');

     return $collection;

    }

    public function getPaymentType($paymentInstance){

        //echo "<br >get payment type ". $paymentInstance->getMethod();
        $type = "";
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
            case "paybympcatch":
                $type = 'CA';
                break;
            case "zippayment":
                $type = "ZM";
                break;
            case "zipmoneypayment":
                $type = "ZM";
                break;
            case "zipmoney":
                $type = "ZM";
                break;
            case "zip":
                $type = "ZM";
                break;
            case "braintree_googlepay":
                $type = "BT";
                break;
            case "klarna_kp":
                $type = "KL";
                break;
            default:
                break;
        }

        return $type;
    }

    public function getWarehouse(OrderInterface $order) {
        if (!isset($this->warehouseCode[$order->getEntityId()])) {
            $whse = '';

            if ($order->getShippingMethod() == 'collect_collect') {
                if ($collectPlaceId = $this->getCollectPlaceId($order)) {
                    $whse = $this->abstractEntityRepository->getById($collectPlaceId)->getCode();
                    //$whse = $this->repCodeForPickUp[$collectPlaceId];
                }
            } elseif ($order->getShippingAddress()) {
//                $whse = $this->getWarehouseByRegionCode($order->getShippingAddress()->getRegionCode());
//                $skus = $this->getProductsSkus($order);
//                if (!$this->isProductsInStock($whse, $skus) && isset($this->relocateWarehouseMap[$whse]) && $this->isProductsInStock($this->relocateWarehouseMap[$whse], $skus)) {
//                    $whse = $this->relocateWarehouseMap[$whse];
//                }
                //requestd by Emmanuel
                $whse = "SWHS";
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
        //echo 'Rep - '. $this->repDispatchWarehouseMap[$this->getWarehouse($order)] ?? '';
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

    public function prontoStatus(OrderInterface $order) {
        $status = $order->getState();
        $prontoStatus = "I";
        if($status == "holded")
        {
            $prontoStatus = "H";
        }

        return $prontoStatus;
    }

    public function orderPostByDay()
    {

        //get order data
        $orders = $this->getTestOrderCollectionByDay();
        $counter = 0;
        foreach ($orders as $order)
        {
            $data = array();
            $counter++;

            /* @var $order \Magento\Sales\Model\Order */

            if ($order->getState() == 'canceled') {
                continue;
            }

            $orderId = $order->getIncrementId();
            $entityId = $order->getId();
            $this->logger->info('Pronto Order Sync - '.$orderId);

            //Amazon Logic
            $wrehs = $this->getWarehouse($order);
            $territory = "WEBS";
            if($wrehs != 'SWHS')
            {
                $territory = $wrehs;
            }
            $accountname = $this->getAccountName($order);
            $account = $this->getAccount($order);
            $address = $order->getBillingAddress();
            $countrycode = $address->getCountryId();
            $amShipping = $order->getShippingDescription();
            $is_am_order = false;
            $is_am_fba = false;
            if (strpos($orderId, 'AM') !== false) {
                $is_am_order = true;
            }

            if($is_am_order){
                $rep = "AMAZON MFN";
                if (strpos($amShipping, 'AFN') !== false) {
                    $rep = "AMAZON FBA";
                    $account = "AMAZ00";
                    if($countrycode == "NZ")
                    {
                        $account = "AMAZ01";
                    }

                    $wrehs = "AWHS";
                    $territory = "AWHS";
                    $is_am_fba = true;
                }

            }
            else
            {
                $rep = $this->getRep($order);
                if (strpos($orderId, 'EB') !== false) {
                    $rep ="EBAY";
                }
                else if (strpos($orderId, 'CATCH') !== false) {
                    $rep ="CATCH";
                }
                else if (strpos($orderId, 'MYD') !== false) {
                    $rep ="MYDEAL";
                }

            }

            $contactname = $accountname;
            //check pronto if customer has an account.
            //if not, create customer account to pronto
            $created = $order->getCreatedAt();
            $created = $this->timezone->date(new \DateTime($created));
            $orderdate = $created->format('Y-m-d');

            $customerEmail = $order->getCustomerEmail();
            $data['sales-order']['header']['accountname'] = $accountname;
            $data['sales-order']['header']['account'] = $account;
            $data['sales-order']['header']['order-date'] = $orderdate;
            $data['sales-order']['header']['warehouse'] = $wrehs;
            $data['sales-order']['header']['customer-type'] = "WC";
            $data['sales-order']['header']['so-cust-type'] = "WC";
            $data['sales-order']['header']['territory'] = $territory;
            $data['sales-order']['header']['rep'] = $rep;
            $data['sales-order']['header']['contactname'] = $contactname;
            $data['sales-order']['header']['email'] = $customerEmail;
            $data['sales-order']['header']['reference'] = $entityId;
            $data['sales-order']['header']['on-hold-reason-code'] = "01";
            $data['sales-order']['header']['set-on-status'] = "H";
            $data['sales-order']['header']['so-part-shipment-allowed'] = "N";

            //echo "<br> WH - ".$data['sales-order']['header']['warehouse'];
            $grandTotal = (double) $order->getBaseGrandTotal();
            $subTotal = (double) $order->getBaseSubtotalInclTax();
            $tax = (double) $order->getBaseTaxAmount();
            $shipping = (double) $order->getBaseShippingInclTax();

            $data['sales-order']['header']['order-total-inc-tax'] = $grandTotal;

            $strt = $address->getStreet();
            if(is_array($strt))
            {
                $street = implode(",", $strt);
            }
            $city = $address->getCity();
            $region = $address->getRegion();
            $postcode = $address->getPostcode();
            $countrycode = $address->getCountryId();
            $phone = $address->getTelephone();
            $mobile = $address->getMobile();
            $company = $address->getCompany();
            $unitNumber = $address->getUnitNumber();
            if(!empty($unitNumber))
            {
                $unitNumber = str_replace("unit_number"," ",$unitNumber);
            }

            $data['sales-order']['header']['billing-address']['line-1'] = $company;
            $data['sales-order']['header']['billing-address']['line-2'] = $unitNumber." ".$street;
            $data['sales-order']['header']['billing-address']['line-3'] = $city;
            $data['sales-order']['header']['billing-address']['line-4'] = $region;
            $data['sales-order']['header']['billing-address']['postcode'] = $postcode;
            $data['sales-order']['header']['billing-address']['country-code'] = $countrycode;
            $data['sales-order']['header']['billing-address']['phone'] = $phone;
            $data['sales-order']['header']['billing-address']['mobile'] = $mobile;

            $shipaddress = $order->getShippingAddress();
            $shipstrt = $shipaddress->getStreet();
            if(is_array($shipstrt))
            {
                $shipstreet = implode(",", $shipstrt);
            }
            $shipcity = $shipaddress->getCity();
            $shipregion = $shipaddress->getRegion();
            $shippostcode = $shipaddress->getPostcode();
            $shipcountrycode = $shipaddress->getCountryId();
            $shipphone = $shipaddress->getTelephone();
            $shipmobile = $shipaddress->getMobile();
            $shipcompany = $shipaddress->getCompany();
            $shipUnitNumber = $shipaddress->getUnitNumber();
            if(!empty($shipUnitNumber))
            {
                $shipUnitNumber = str_replace("unit_number"," ",$shipUnitNumber);
            }

            $data['sales-order']['header']['delivery-address']['line-1'] = $contactname;
            $data['sales-order']['header']['delivery-address']['line-2'] = $shipcompany;
            $data['sales-order']['header']['delivery-address']['line-3'] = $shipUnitNumber." ".$shipstreet;
            $data['sales-order']['header']['delivery-address']['line-4'] = $shipcity;
            $data['sales-order']['header']['delivery-address']['line-5'] = $shipregion;
            $data['sales-order']['header']['delivery-address']['postcode'] = $shippostcode;
            $data['sales-order']['header']['delivery-address']['country-code'] = $shipcountrycode;
            $data['sales-order']['header']['delivery-address']['phone'] = $shipphone;
            $data['sales-order']['header']['delivery-address']['mobile'] = $shipmobile;

            $paymentInstance = $order->getPayment();

            //payment details

            //$methodInst = $paymentInstance->getMethodInstance();
            $method = $paymentInstance->getMethod();

            $payment_type = $this->getPaymentType($paymentInstance);
            $cc = "";
            if($payment_type == 'BT')
            {
                $cc = $paymentInstance->getCcType();
            }

            $payment_reference = $paymentInstance->getLastTransId();

//            if (empty($payment_reference) && ($method == 'm2epropayment')) {
//                $payment_reference = $paymentInstance->getAdditionalInformation('channel_order_id');
//            }
            //ebay
            if (($method == 'm2epropayment')) {
                if($paymentInstance->getAdditionalInformation('component_mode') == 'ebay')
                {
                    $payment_reference = $paymentInstance->getAdditionalInformation('channel_order_id');
                }
                else if (empty($payment_reference))
                {
                    $payment_reference = $paymentInstance->getAdditionalInformation('channel_order_id');
                }
            }
            //work around for IR orders coming as H
            if($payment_type == 'H')
            {
                if (strpos($orderId, 'CATCH') !== false) {
                    $payment_type ="CA";
                    $catchRef = $orderId;
                    $catchRef = str_replace("CATCH","",$catchRef);
                    $payment_reference = $catchRef;
                }
                else if (strpos($orderId, 'MYD') !== false) {
                    $payment_type ="MD";
                    $catchRef = $orderId;
                    $catchRef = str_replace("MYD","",$catchRef);
                    $payment_reference = $catchRef;
                }
                else if (strpos($orderId, 'AM') !== false) {
                    $payment_type ="AM";
                    $catchRef = $orderId;
                    $catchRef = str_replace("AM","",$catchRef);
                    $payment_reference = $catchRef;
                }
            }

            $withpaymentref = true;
            if(($payment_type == "Y"))
            {
                $withpaymentref = false;
            }
            if(($payment_type == "H"))
            {
                $withpaymentref = false;
            }
            if(($payment_type == "VI"))
            {
                $withpaymentref = false;
            }

            $amount_tendered = $order->getBaseGrandTotal();
            $amount_tendered = round($amount_tendered, 2);
            if((!$is_am_fba))
            {
                if($withpaymentref)
                {
                    $data['sales-order']['header']['payment-details']['payment-detail']['payment-type'] = $payment_type;
                    $data['sales-order']['header']['payment-details']['payment-detail']['payment-reference'] = $payment_reference." ".$cc;
                    $data['sales-order']['header']['payment-details']['payment-detail']['amount-tendered'] = $amount_tendered;
                }

            }

            //CUSTOM DATA
            $qffNumber = $order->getQffNumber();
            $qffLastname = $order->getQffLastname();
            if (!empty($qffNumber) && !empty($qffLastname)) {
                $data['sales-order']['header']['custom-data']['data'][0]['key'] = 'QFF';
                $data['sales-order']['header']['custom-data']['data'][0]['value'] = $qffNumber;
                $data['sales-order']['header']['custom-data']['data'][1]['key'] = 'QFFSURNAME';
                $data['sales-order']['header']['custom-data']['data'][1]['value'] = $qffLastname;
            }
            else
            {
                $data['sales-order']['header']['custom-data']['data'][0]['key'] = 'QFF';
                $data['sales-order']['header']['custom-data']['data'][0]['value'] = NULL;
                $data['sales-order']['header']['custom-data']['data'][1]['key'] = 'QFFSURNAME';
                $data['sales-order']['header']['custom-data']['data'][1]['value'] = NULL;
            }

            $data['sales-order']['header']['custom-data']['data'][2]['key'] = 'magento-order-number';
            $data['sales-order']['header']['custom-data']['data'][2]['value'] = $orderId;

            $data['sales-order']['header']['custom-data']['data'][3]['key'] = 'email';
            $data['sales-order']['header']['custom-data']['data'][3]['value'] = $customerEmail;

            //for coupon
            $coupon = $order->getCouponCode();
            $couponDiscount = ((double) $order->getBaseDiscountAmount());

            //product lines
            $x = 0;
            foreach ($order->getAllVisibleItems() as $item) {
                /* @var $item \Magento\Sales\Model\Order\Item */

                $skus = array();
                $productSku = "";
                $digiProtect = "";
                $price = (double) $item->getBasePriceInclTax();
                $qty = (double) $item->getQtyOrdered();
                $discount = (double) $item->getDiscountAmount();
                $total = ($price * $qty) - $discount;
                if($coupon != "")
                {
                    $discount = 0;
                }
                $digiProtectPrice = 0;
                $digiProtectQty = 0;
                $digiProtectdiscount = 0;
                $digiProtectTotal = 0;

                $sku = $item->getSku();
                if(strpos($sku, '-') !== false)
                {
                    $skus = explode('-', $sku);
                    $productSku = $skus[0];
                    $digiProtect = $skus[1];

                    $price = (double) $item->getBasePriceInclTax();
                    $orig = (double) $item->getOriginalPrice();
                    $digiProtectPrice = $price - $orig;
                    $digiProtectQty = (double) $item->getQtyOrdered();
                    $digiProtectdiscount = (double) $item->getDiscountAmount();
                    if($coupon != "")
                    {
                        $digiProtectdiscount = 0;
                    }
                    $digiProtectTotal = ($digiProtectPrice * $digiProtectQty) - $digiProtectdiscount;

                }
                else
                {
                    $productSku = $sku;

                }


                $data['sales-order']['detail']['line'][$x]['line-type'] = 'SN';
                $data['sales-order']['detail']['line'][$x]['stock-code'] = $productSku;
                $data['sales-order']['detail']['line'][$x]['description'] = $item->getName();
                $data['sales-order']['detail']['line'][$x]['unit-price-inc-tax'] = $price;
                $data['sales-order']['detail']['line'][$x]['ordered'] = $qty;
                $data['sales-order']['detail']['line'][$x]['shipped'] = 0;
                $data['sales-order']['detail']['line'][$x]['backordered'] = $qty;
                $data['sales-order']['detail']['line'][$x]['sol-disc-rate'] = $discount;
                $data['sales-order']['detail']['line'][$x]['sol-line-total-inc-tax'] = $total;
                $x++;

                if(!empty($digiProtect))
                {
                    $price = (double) $item->getBasePriceInclTax();
                    $qty = (double) $item->getQtyOrdered();
                    $discount = (double) $item->getDiscountAmount();
                    $total = ($price * $qty) - $discount;
                    $data['sales-order']['detail']['line'][$x]['line-type'] = 'SN';
                    $data['sales-order']['detail']['line'][$x]['stock-code'] = $digiProtect;
                    $data['sales-order']['detail']['line'][$x]['description'] = "digiProtect";
                    $data['sales-order']['detail']['line'][$x]['unit-price-inc-tax'] = $digiProtectPrice;
                    $data['sales-order']['detail']['line'][$x]['ordered'] = $digiProtectQty;
                    $data['sales-order']['detail']['line'][$x]['shipped'] = 0;
                    $data['sales-order']['detail']['line'][$x]['backordered'] = $digiProtectQty;
                    $data['sales-order']['detail']['line'][$x]['sol-disc-rate'] = $digiProtectdiscount;
                    $data['sales-order']['detail']['line'][$x]['sol-line-total-inc-tax'] = $digiProtectTotal;
                    $x++;
                }
            }


            if($coupon != "")
            {
                $data['sales-order']['detail']['line'][$x]['line-type'] = 'SC';
                $data['sales-order']['detail']['line'][$x]['description'] = $coupon;
                $data['sales-order']['detail']['line'][$x]['unit-price-inc-tax'] = $couponDiscount;
                $data['sales-order']['detail']['line'][$x]['ordered'] = 1;
                $data['sales-order']['detail']['line'][$x]['shipped'] = 1;
                $data['sales-order']['detail']['line'][$x]['sol-disc-rate'] = 0;
                $data['sales-order']['detail']['line'][$x]['sol-chg-type'] = 5;
                $data['sales-order']['detail']['line'][$x]['sol-line-total-inc-tax'] = $couponDiscount;
                $x++; // for shipping counter
            }

            $shippingprice = (double) $order->getShippingAmount();
            $shippingDesc = $order->getShippingDescription();
            if (strpos($shippingDesc, '|') !== false) {
                $marketplacesShipping = explode('|', $shippingDesc);
                $shippingDesc = $marketplacesShipping[1];
            }
            if($shippingDesc == "Express - (1 to 3 Days)")
            {
                $shippingDesc = "Australia Post – express";
            }
            else if($shippingDesc == "Standard - (4 to 7 Days)")
            {
                $shippingDesc = "Australia Post – eParcel";
            }

            //shipping details
            $data['sales-order']['detail']['line'][$x]['line-type'] = 'SC';
            $data['sales-order']['detail']['line'][$x]['description'] = $shippingDesc;
            $data['sales-order']['detail']['line'][$x]['unit-price-inc-tax'] = $shippingprice;
            $data['sales-order']['detail']['line'][$x]['ordered'] = 1;
            $data['sales-order']['detail']['line'][$x]['shipped'] = 1;
            $data['sales-order']['detail']['line'][$x]['sol-disc-rate'] = 0;
            $data['sales-order']['detail']['line'][$x]['sol-chg-type'] = 0;
            $data['sales-order']['detail']['line'][$x]['sol-line-total-inc-tax'] = $shippingprice;

            //create xml of order data here
            $this->logger->info('Pronto Order Sync Data - ',$data['sales-order']);
            $xml = \Digidirect\AI\Model\Lib\Adapter\Import\Xml::assocToXml($data, 'sales-orders');

            //TEST
            //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/sales?call-type=create_orders'; //TEST

            //LIVE - port :8084
            $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/sales?call-type=create_orders';


            $islive = true;
            if($islive)
            {
                $this->curl->addHeader("Content-Type", "application/xml");
                $this->curl->addHeader("Accept", "application/json");
                $this->curl->addHeader("compcode", "DIG"); //live
                $this->curl->addHeader("user", "ewaveapi");
                $this->curl->addHeader("token", "904241bdbf10efa9");
                //
                //$this->curl->addHeader("compcode", "UA1"); //test
                //$this->curl->addHeader("user", "clint.mercado");
                //$this->curl->addHeader("token", "849cd5080faff5ce");

                $this->curl->post($url, $xml);

                $result = $this->curl->getBody();

                $json = $this->jsonSerializer->unserialize($result);

                if(isset($json['response']['status']) && ($json['response']['status'] == 'FAIL'))
                {
                    $msg =  $json['response']['message'];
                    //echo $msg."<br>";
                    $order->setData('pronto_order_number',$msg);
                    $order->save();
                    $this->logger->error('Pronto Order Sync', array('info' => $msg));

                }
                else if (isset($json['sales-orders']['response']['status']) && ($json['sales-orders']['response']['status'] == 'failed')) {
                    $msg =  $json['sales-orders']['response']['message'];
                    //echo $msg ."<br>";
                    $order->setData('pronto_order_number',$msg);
                    $order->save();
                    $this->logger->error('Pronto Order Sync', array('info' => $msg));

                }
                else {

                    $pronto = $json['sales-orders']['sales-order']['order-no'];
                    $invoiceno = $json['sales-orders']['sales-order']['invoice-no'];
                    $prontostatus = $json['sales-orders']['sales-order']['order-status-code'];
                    $order->setData('pronto_order_number',$pronto);
                    $order->setData('pronto_status_code',$prontostatus);
                    $order->save();


                    $this->logger->info('Pronto Order Sync ', $json['sales-orders']['sales-order']);

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

                }
            }

            if($counter >= 3)
            {
                return true; //return after 2 orders
            }

        }

        return true;
    }

    public function getTestOrderCollectionByDay()
    {
        $date = '2021-08-02';
        $fromDate = date('Y-m-d'. ' 00:00:00',strtotime($date));
        $toDate = date('Y-m-d'. ' 23:59:59',strtotime($date));
        $collection = $this->_orderCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('pronto_order_number', array('null' => true))
            ->addFieldToFilter('created_at', array('gteq' => $fromDate))
            ->addFieldToFilter('created_at', array('lteq' => $toDate));
        return $collection;
    }
}
