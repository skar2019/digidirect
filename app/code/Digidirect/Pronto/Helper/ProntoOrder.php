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
use Magento\Directory\Model\ResourceModel\Region\Collection;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;

class ProntoOrder extends AbstractHelper
{

    /**
     * @var Curl
     */
    protected $curl;

    protected $_orderCollectionFactory;

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


    private $timezone;

    /**
     * @var Country
     */
    public $countryFactory;

    protected $productDigiprot;

    protected $storeManager;

    protected $customerFactory;

    protected $quote;

    protected $quoteManagement;

    protected $productRepository;

    private $collectionFactory;

    protected $shippingRate;

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
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        CountryFactory $countryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        CollectionFactory $collectionFactory,
        \Magento\Quote\Model\Quote\Address\Rate $shippingRate)
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
        $this->timezone = $timezone;
        $this->countryFactory = $countryFactory;
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
        $this->productRepository = $productRepository;
        $this->collectionFactory = $collectionFactory;
        $this->shippingRate = $shippingRate;

    }

    public function GetProntoOrders($status)
    {

        $data = array();
        $dataxml = array();
        //shipping details
        $dataxml['sales']='';
//            $data['sales-order']['detail']['line'][$x]['description'] = $shippingDesc;
//            $data['sales-order']['detail']['line'][$x]['unit-price-inc-tax'] = $shippingprice;
//            $data['sales-order']['detail']['line'][$x]['ordered'] = 1;
//            $data['sales-order']['detail']['line'][$x]['shipped'] = 1;
//            $data['sales-order']['detail']['line'][$x]['sol-disc-rate'] = 0;
//            $data['sales-order']['detail']['line'][$x]['sol-chg-type'] = "C1";
//            $data['sales-order']['detail']['line'][$x]['sol-line-total-inc-tax'] = $shippingprice;

        //create xml of order data here
        //$this->logger->info('Pronto Order Sync Data - ',$data['sales-order']);
        $xml = \Digidirect\AI\Model\Lib\Adapter\Import\Xml::assocToXml($dataxml, 'sales');

        //TEST
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/login'; //TEST

        $this->curl->addHeader("Content-Type", "application/xml");
        $this->curl->addHeader("Accept", "application/xml");

        $this->curl->addHeader("X-Pronto-Username", "clint.mercado");
        $this->curl->addHeader("X-Pronto-Password", "849Cd5080faff5ce");
        $this->curl->post($url, $xml);

        $result = $this->curl->getBody();
        $xml=simplexml_load_string($result);
        $token = $xml->token;
        echo $token ."\n";
        $this->curl->addHeader("Content-Type", "application/xml");
        $this->curl->addHeader("Accept", "application/xml");

        $this->curl->addHeader("X-Pronto-Token", $token);
        //Filters TerritoryCode not working
//        $data['Filters']['TerritoryCode']['Like']='SYDN%';
//        $data['Filters']['TerritoryCode']['Like']='MELB%';
//        $data['Filters']['TerritoryCode']['Like']='BRIS%';
//        $data['Filters']['TerritoryCode']['Like']='MIRA%';
        $data['Filters']['StatusCode']['Like']=$status;

        //$data['Filters']['TerritoryCode']['Like']='BOND%';
        //$data['Filters']['TerritoryCode']['Like']='PARR%';
        $data['Filters']['TerritoryCode']['NotLike']='WEBS%';

        $data['RequestFields']['SalesOrders']['SalesOrder']['SOOrderNo']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['CustomerCode']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['CustomerEmail']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['Address1']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['Address2']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['Address3']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['Address4']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['Address5']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['AddressPostcode']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['CustomerName']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['InvoiceNo']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['OrderedAmountIncTax']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['Contact']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['Date']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['TerritoryCode']='';

        $xmldata = \Digidirect\AI\Model\Lib\Adapter\Import\Xml::assocToXml($data, 'SalesOrderGetSalesOrdersRequest');

        //https://digi-pronto.abtonline.com.au:443/pronto/rest/ua1.salesorder/api/SalesOrderGetSalesOrders
        $urldata = 'https://digi-pronto.abtonline.com.au:443/pronto/rest/dig.salesorder/api/SalesOrderGetSalesOrders';
        //$urldata = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/sales?call-type=get_order';
        $this->curl->post($urldata, $xmldata);

        $resultdata = $this->curl->getBody();
        if(is_null($resultdata))
        {
            exit;
        }

        $xmlresult = simplexml_load_string($resultdata);
        $x = 0;

        if(is_null($xmlresult))
        {
            exit;
        }

        foreach($xmlresult->SalesOrders->SalesOrder as $orderdata)
        {
            $TerritoryCode = $orderdata->TerritoryCode;
            if($TerritoryCode == "MRKT" || $TerritoryCode == "WEBS")
            {
                continue;
            }

            $x++;

            $email = (string)$orderdata->CustomerEmail;
            if(empty($email))
            {
                continue;
            }

            if (str_contains($email, 'westfield.com')) {
                continue;
            }
            if (str_contains($email, 'catch.com.au')) {
                continue;
            }
            if (str_contains($email, 'marketplace.amazon.com.au')) {
                continue;
            }
            if (str_contains($email, 'mydeal.com.au')) {
                continue;
            }
            if (str_contains($email, 'members.ebay.com')) {
                continue;
            }


            $name = explode(" ",$orderdata->CustomerName);
            $firstname = $name[0];
            $lastname = $name[1];

            if(isset($name[2]))
            {
                $lastname = $name[2];
            }

            $soorderno = $orderdata->SOOrderNo;
            echo $soorderno . "\n";
            $street = (string)$orderdata->Address2;
            if(empty($street))
            {
                $street = "N/A";
            }
            $city = (string)$orderdata->Address3;
            if(empty($city))
            {
                $city = "N/A";
            }
            $region = (string)$orderdata->Address4;
            if(empty($region))
            {
                $region = "Victoria";
            }
            $postcode = (string)$orderdata->AddressPostcode;
            if(empty($postcode))
            {
                $postcode = "N/A";
            }
            echo $region ."\n";
            switch ($region) {
                case "QLD":
                    $region = 'Queensland';
                    break;
                case "VIC":
                    $region = 'Victoria';
                    break;
                case "NSW":
                    $region = 'New South Wales';
                    break;
                case "WA":
                    $region = 'Western Australia';
                    break;
                default:
                    $region = "South Australia";
                    break;
            }

            $regiondetails = $this->getRegionCode($region);
            //var_dump($regiondetails);
            $regionId = $regiondetails['region_id'];

            $orderInfo = [
                'currency_id'  => 'AUD',
                'email'        => (string)$orderdata->CustomerEmail, //customer email id
                'address' =>[
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'prefix' => '',
                    'suffix' => '',
                    'street' => $street,
                    'city' => $city,
                    'country_id' => 'AU',
                    'region' => $region,
                    'region_id' => $regionId,
                    'postcode' => $postcode,
                    'telephone' => '123456789',
                    'save_in_address_book' => 1
                ],
                'pronto_account_id' => (string)$orderdata->SOOrderNo,
                'createdate'=> (string)$orderdata->Date

            ];

            //loop thru item
            $this->curl->addHeader("Content-Type", "application/xml");
            $this->curl->addHeader("Accept", "application/xml");
            $this->curl->addHeader("X-Pronto-Token", $token);

            $orderlinedata = array();
            $orderlinedata['Parameters']['SOOrderNo']= $soorderno;

            $orderlinedata['RequestFields']['SalesOrderLines']['SalesOrderLine']['ItemCode']='';
            $orderlinedata['RequestFields']['SalesOrderLines']['SalesOrderLine']['OrderedQty']='';

            $orderlinexml = \Digidirect\AI\Model\Lib\Adapter\Import\Xml::assocToXml($orderlinedata, 'SalesOrderGetSalesOrderLinesRequest');

            $urlorderline = 'https://digi-pronto.abtonline.com.au:443/pronto/rest/dig.salesorder/api/SalesOrderGetSalesOrderLines';//'https://digi-pronto.abtonline.com.au:443/pronto/rest/ua1.salesorder/api/SalesOrderGetSalesOrderLines';
            $this->curl->post($urlorderline, $orderlinexml);
            $resultorderline = $this->curl->getBody();
            $xmlline = simplexml_load_string($resultorderline);

            $items = array();
            $x = 0;
            foreach($xmlline->SalesOrderLines->SalesOrderLine as $orderline)
            {
                if(!empty($orderline->ItemCode))
                {
                    $itemcode = (string)$orderline->ItemCode;
                    $quantity = (int)$orderline->OrderedQty;
                    $items['items'][$x] = array('sku'=>$itemcode,'qty'=>$quantity);
                    echo $itemcode ."\n";
                }
                $x++;
            }

            array_push($orderInfo, $items);

            $orders = $this->getOrderCollection($orderInfo['pronto_account_id']);
            $order_exists = false;
            foreach ($orders as $order)
            {
                $order_exists = true;
                echo "order exist - ".$orderInfo['pronto_account_id'];
            }

            if(!$order_exists)
            {
                $orderresult = $this->createOrder($orderInfo);
                //var_dump($orderresult);
            }

        }
        exit;

    }



    public function createOrder($orderInfo)
    {
        $store = $this->storeManager->getStore(7); //from backend, retail store id 7 on staging2 //6 on my local
        $storeId = $store->getStoreId();
        echo "store id ".$storeId."\n <br/>";
        $websiteId = 1;//$this->storeManager->getStore()->getWebsiteId(); //10 on staging2 //6 on my local
        echo "website id ".$websiteId."\n <br/>";
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        //$customer->setWebsiteId(1); // use 1 for digidirect store work around so it will not create new customer on different store
        echo "customer email ".$orderInfo['email']." <br/>";
        $customer->loadByEmail($orderInfo['email']);// load customet by email address
        if(!$customer->getId()){
            echo "create customer \n <br/>";
            //For guest customer create new cusotmer
            $customer->setWebsiteId($websiteId)
                ->setStore($store)
                ->setFirstname($orderInfo['address']['firstname'])
                ->setLastname($orderInfo['address']['lastname'])
                ->setEmail($orderInfo['email']);

            $customer->setData('pronto_account_id', $orderInfo['pronto_account_id']);
            $customer->setCustomAttribute('pronto_account_id', $orderInfo['pronto_account_id']);
            $customer->save();
        }

        echo "to quote <br />";
        $quote=$this->quote->create(); //Create object of quote
        $quote->setStore($store); //set store for our quote
        /* for registered customer */
        $customer = $this->customerRepository->getById($customer->getId());
        $quote->setCurrency();
        $quote->assignCustomer($customer); //Assign quote to customer
        echo "assign Customer <br />";
        //add items in quote
        foreach($orderInfo[0]['items'] as $item){
            echo "to add product <br />";
            $product = $this->productRepository->get($item['sku']);
            /* for simple product */
            $quote->addProduct($product,intval($item['qty']));
            echo "add product <br />";
        }

        //Set Billing and shipping Address to quote
        $quote->getBillingAddress()->addData($orderInfo['address']);
        $quote->getShippingAddress()->addData($orderInfo['address']);
        echo "billing and shipping <br />";
        // set shipping method
        // Collect Rates and Set Shipping & Payment Method
        $this->shippingRate
            ->setCode('flatrate')
            ->getPrice(1);
        $shippingAddress=$quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('flatrate'); //shipping method, please verify flat rate shipping must be enable

        $quote->getShippingAddress()->addShippingRate($this->shippingRate);

        $quote->setPaymentMethod('checkmo'); //payment method, please verify checkmo must be enable from admin
        $quote->setInventoryProcessed(false); //decrease item stock equal to qty
        $quote->save(); //quote save
        // Set Sales Order Payment, We have taken check/money order
        $quote->getPayment()->importData(['method' => 'checkmo']);

        // Collect Quote Totals & Save
        $quote->collectTotals()->save();
        // Create Order From Quote Object
        $order = $this->quoteManagement->submit($quote);

        echo "quote submitted <br />";
        /* get order real id from order */
        $orderId = $order->getIncrementId();

        $order->setCreatedAt($orderInfo['createdate']);
        $order->setData('pronto_order_number',$orderInfo['pronto_account_id']);

        $order->setState(\Magento\Sales\Model\Order::STATE_COMPLETE)
            ->setStatus($order->getConfig()->getStateDefaultStatus(\Magento\Sales\Model\Order::STATE_COMPLETE))
            ->save();

        if($orderId){
            echo "order id ".$orderId."<br/>";
            $result['success']= $orderId;
        }else{
            echo "error <br/>";
            $result=['error'=>true,'msg'=>'Error occurs for Order placed'];
        }
        return $result;
    }

    public function getRegionCode(string $region): array
    {
        $regionCode = $this->collectionFactory->create()
            ->addRegionNameFilter($region)
            ->getFirstItem()
            ->toArray();
        return $regionCode;
    }

    public function getOrderCollection($pronto)
    {

        $collection = $this->_orderCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('pronto_order_number', array('eq' => $pronto));

        return $collection;

    }

    public function debugGetProntoOrders($status)
    {

        $data = array();
        $dataxml = array();
        //shipping details
        $dataxml['sales']='';
//            $data['sales-order']['detail']['line'][$x]['description'] = $shippingDesc;
//            $data['sales-order']['detail']['line'][$x]['unit-price-inc-tax'] = $shippingprice;
//            $data['sales-order']['detail']['line'][$x]['ordered'] = 1;
//            $data['sales-order']['detail']['line'][$x]['shipped'] = 1;
//            $data['sales-order']['detail']['line'][$x]['sol-disc-rate'] = 0;
//            $data['sales-order']['detail']['line'][$x]['sol-chg-type'] = "C1";
//            $data['sales-order']['detail']['line'][$x]['sol-line-total-inc-tax'] = $shippingprice;

        //create xml of order data here
        //$this->logger->info('Pronto Order Sync Data - ',$data['sales-order']);
        $xml = \Digidirect\AI\Model\Lib\Adapter\Import\Xml::assocToXml($dataxml, 'sales');

        //TEST
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/login'; //TEST

        $this->curl->addHeader("Content-Type", "application/xml");
        $this->curl->addHeader("Accept", "application/xml");

        $this->curl->addHeader("X-Pronto-Username", "clint.mercado");
        $this->curl->addHeader("X-Pronto-Password", "849Cd5080faff5ce");
        $this->curl->post($url, $xml);

        $result = $this->curl->getBody();
        $xml=simplexml_load_string($result);
        $token = $xml->token;
        echo $token ."\n";
        $this->curl->addHeader("Content-Type", "application/xml");
        $this->curl->addHeader("Accept", "application/xml");

        $this->curl->addHeader("X-Pronto-Token", $token);
        //Filters TerritoryCode not working
//        $data['Filters']['TerritoryCode']['Like']='SYDN%';
//        $data['Filters']['TerritoryCode']['Like']='MELB%';
//        $data['Filters']['TerritoryCode']['Like']='BRIS%';
//        $data['Filters']['TerritoryCode']['Like']='MIRA%';
        $data['Filters']['StatusCode']['Like']=$status;

        //$data['Filters']['TerritoryCode']['Like']='BOND%';
        //$data['Filters']['TerritoryCode']['Like']='PARR%';
        $data['Filters']['TerritoryCode']['NotLike']='WEBS%';

        $data['RequestFields']['SalesOrders']['SalesOrder']['SOOrderNo']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['CustomerCode']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['CustomerEmail']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['Address1']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['Address2']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['Address3']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['Address4']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['Address5']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['AddressPostcode']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['CustomerName']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['InvoiceNo']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['OrderedAmountIncTax']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['Contact']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['Date']='';
        $data['RequestFields']['SalesOrders']['SalesOrder']['TerritoryCode']='';

        $xmldata = \Digidirect\AI\Model\Lib\Adapter\Import\Xml::assocToXml($data, 'SalesOrderGetSalesOrdersRequest');

        //https://digi-pronto.abtonline.com.au:443/pronto/rest/ua1.salesorder/api/SalesOrderGetSalesOrders
        $urldata = 'https://digi-pronto.abtonline.com.au:443/pronto/rest/dig.salesorder/api/SalesOrderGetSalesOrders';
        //$urldata = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/sales?call-type=get_order';
        $this->curl->post($urldata, $xmldata);

        $resultdata = $this->curl->getBody();
        var_dump($resultdata);

        if(is_null($resultdata))
        {
            exit;
        }

        $xmlresult = simplexml_load_string($resultdata);
        $x = 0;

        if(is_null($xmlresult))
        {
            exit;
        }

        foreach($xmlresult->SalesOrders->SalesOrder as $orderdata)
        {
            $TerritoryCode = $orderdata->TerritoryCode;
            if($TerritoryCode == "MRKT" || $TerritoryCode == "WEBS")
            {
                continue;
            }

            $x++;

            $email = (string)$orderdata->CustomerEmail;
            if(empty($email))
            {
                continue;
            }

            if (str_contains($email, 'westfield.com')) {
                continue;
            }
            if (str_contains($email, 'catch.com.au')) {
                continue;
            }
            if (str_contains($email, 'marketplace.amazon.com.au')) {
                continue;
            }
            if (str_contains($email, 'mydeal.com.au')) {
                continue;
            }
            if (str_contains($email, 'members.ebay.com')) {
                continue;
            }


            $name = explode(" ",$orderdata->CustomerName);
            $firstname = $name[0];
            $lastname = $name[1];

            if(isset($name[2]))
            {
                $lastname = $name[2];
            }

            $soorderno = $orderdata->SOOrderNo;
            echo $soorderno . "\n";
            $street = (string)$orderdata->Address2;
            if(empty($street))
            {
                $street = "N/A";
            }
            $city = (string)$orderdata->Address3;
            if(empty($city))
            {
                $city = "N/A";
            }
            $region = (string)$orderdata->Address4;
            if(empty($region))
            {
                $region = "Victoria";
            }
            $postcode = (string)$orderdata->AddressPostcode;
            if(empty($postcode))
            {
                $postcode = "N/A";
            }
            echo $region ."\n";
            switch ($region) {
                case "QLD":
                    $region = 'Queensland';
                    break;
                case "VIC":
                    $region = 'Victoria';
                    break;
                case "NSW":
                    $region = 'New South Wales';
                    break;
                case "WA":
                    $region = 'Western Australia';
                    break;
                default:
                    $region = "South Australia";
                    break;
            }

            $regiondetails = $this->getRegionCode($region);
            //var_dump($regiondetails);
            $regionId = $regiondetails['region_id'];

            $orderInfo = [
                'currency_id'  => 'AUD',
                'email'        => (string)$orderdata->CustomerEmail, //customer email id
                'address' =>[
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'prefix' => '',
                    'suffix' => '',
                    'street' => $street,
                    'city' => $city,
                    'country_id' => 'AU',
                    'region' => $region,
                    'region_id' => $regionId,
                    'postcode' => $postcode,
                    'telephone' => '123456789',
                    'save_in_address_book' => 1
                ],
                'pronto_account_id' => (string)$orderdata->SOOrderNo,
                'createdate'=> (string)$orderdata->Date

            ];

            //loop thru item
            $this->curl->addHeader("Content-Type", "application/xml");
            $this->curl->addHeader("Accept", "application/xml");
            $this->curl->addHeader("X-Pronto-Token", $token);

            $orderlinedata = array();
            $orderlinedata['Parameters']['SOOrderNo']= $soorderno;

            $orderlinedata['RequestFields']['SalesOrderLines']['SalesOrderLine']['ItemCode']='';
            $orderlinedata['RequestFields']['SalesOrderLines']['SalesOrderLine']['OrderedQty']='';

            $orderlinexml = \Digidirect\AI\Model\Lib\Adapter\Import\Xml::assocToXml($orderlinedata, 'SalesOrderGetSalesOrderLinesRequest');

            $urlorderline = 'https://digi-pronto.abtonline.com.au:443/pronto/rest/dig.salesorder/api/SalesOrderGetSalesOrderLines';//'https://digi-pronto.abtonline.com.au:443/pronto/rest/ua1.salesorder/api/SalesOrderGetSalesOrderLines';
            $this->curl->post($urlorderline, $orderlinexml);
            $resultorderline = $this->curl->getBody();
            $xmlline = simplexml_load_string($resultorderline);

            $items = array();
            $x = 0;
            foreach($xmlline->SalesOrderLines->SalesOrderLine as $orderline)
            {
                if(!empty($orderline->ItemCode))
                {
                    $itemcode = (string)$orderline->ItemCode;
                    $quantity = (int)$orderline->OrderedQty;
                    $items['items'][$x] = array('sku'=>$itemcode,'qty'=>$quantity);
                    echo $itemcode ."\n";
                }
                $x++;
            }

            array_push($orderInfo, $items);

            $orders = $this->getOrderCollection($orderInfo['pronto_account_id']);
            $order_exists = false;
            foreach ($orders as $order)
            {
                $order_exists = true;
                echo "order exist - ".$orderInfo['pronto_account_id'];
            }

            if(!$order_exists)
            {
                $orderresult = $this->createOrder($orderInfo);
                //var_dump($orderresult);
            }

        }
        exit;

    }
}
