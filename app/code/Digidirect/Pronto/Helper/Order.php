<?php

namespace Digidirect\Pronto\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;


class Order extends AbstractHelper
{
 
    /**
    * @var Curl
    */
    protected $curl;
    
    protected $_orderCollectionFactory;

    public function __construct(
                        Curl $curl,
                        JsonSerializer $jsonSerializer,
                        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku,
                        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSaveInterface,
                        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemFactory,
                        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory ) 
                    {
                        $this->curl = $curl;
                        $this->jsonSerializer = $jsonSerializer;
                        $this->sourceItemsBySku = $sourceItemsBySku;
                        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
                        $this->sourceItemFactory = $sourceItemFactory;
                        $this->_orderCollectionFactory = $orderCollectionFactory;

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
//            foreach ($order->getAllVisibleItems() as $item) {
//                /* @var $item \Magento\Sales\Model\Order\Item */
//                $sku = $item->getSku();
//                $name = $item->getName();
//                $price = (double) $item->getBasePriceInclTax();
//                $qty = (double) $item->getQtyOrdered();
//                if (!isset($piwikItems[$sku])) {
//                    $piwikItems[$sku] = [$sku, $name, $price * $qty, $qty];
//                } else {
//                    // Aggregate row total instead of unit price in case there
//                    // are different prices for the same SKU.
//                    $piwikItems[$sku][2] += $price * $qty;
//                    $piwikItems[$sku][3] += $qty;
//                }
//            }
            
            $orderId = $order->getIncrementId();
            //if($orderId == '000001174')
            //{
                
            
            echo "<br />orderId ".$orderId ."<br />";
            $accountname = $order->getCustomerFirstname().$order->getCustomerLastname();
            $contactname = $order->getCustomerFirstname()." ".$order->getCustomerLastname();
            //check pronto if customer has an account.
            //if not, create customer account to pronto
            $data['sales-order']['header']['accountname'] = $accountname;
            $data['sales-order']['header']['account'] = $order->getCustomerId();
            
            $data['sales-order']['header']['order-date'] = "";
            $data['sales-order']['header']['warehouse'] = "SWHS";
            $data['sales-order']['header']['territory'] = "WEBS";
            $data['sales-order']['header']['rep'] = "85";
            $data['sales-order']['header']['contactname'] = $contactname;
            $data['sales-order']['header']['email'] = $order->getCustomerEmail();
            $data['sales-order']['header']['reference'] = $orderId;
            $data['sales-order']['header']['set-on-status'] = "I"; //TO DO get status
            
            
            $grandTotal = (double) $order->getBaseGrandTotal();
            $subTotal = (double) $order->getBaseSubtotalInclTax();
            $tax = (double) $order->getBaseTaxAmount();
            $shipping = (double) $order->getBaseShippingInclTax();
            $discount = abs((double) $order->getBaseDiscountAmount());
            //below not yet used 
            if (empty($piwikOrder)) {
                $piwikOrder = [$orderId, $grandTotal, $subTotal, $tax, $shipping, $discount];
            } else {
                $piwikOrder[0] .= ', ' . $orderId;
                $piwikOrder[1] += $grandTotal;
                $piwikOrder[2] += $subTotal;
                $piwikOrder[3] += $tax;
                $piwikOrder[4] += $shipping;
                $piwikOrder[5] += $discount;
            }
            
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
            
            echo "paymemt - ". $paymentInstance->getMethod();
            if ($paymentInstance->getMethod() == "banktransfer") {
                unset($data);
                continue;
            }
            //var_dump($data);
            if($counter == 2)
            {
                //var_dump($order);
                break;
            }
             
            //}//if order id 000001174
        }
        //should be inside the foreach above
        //create xml of order data here
        $xml = \Digidirect\AI\Model\Lib\Adapter\Import\Xml::assocToXml($data, 'sales-orders');
        
        echo $xml;
        
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/login';
        $url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/sales?call-type=create_orders';
        $username = 'clint.mercado';
        $password = '849cd5080faff5ce';
        $jsonData = '{}';
        
        $this->curl->addHeader("Content-Type", "application/xml");
        $this->curl->addHeader("Accept", "application/json");
        $this->curl->addHeader("compcode", "UA1");
        $this->curl->addHeader("user", "clint.mercado");
        $this->curl->addHeader("token", "849cd5080faff5ce");
        $this->curl->post($url, $xml);
        
        $result = $this->curl->getBody();
        
        
        //var_dump($result);
        // echo $result;
        $json = $this->jsonSerializer->unserialize($result);
        //var_dump($json);
        if(isset($json['response']['status']) && ($json['response']['status'] == 'FAIL'))
        {
            echo $json['response']['message'];
            exit;
        }
        else {
            //success
            //update order data with pronto order-no below
            //$json['sales-order']['sales-order']['order-no']
            echo "success";
            $order->setState("processing")->setStatus("processing");
            $order->save();
        }
        
    }   
    
    public function getOrderCollection()
    {
       $collection = $this->_orderCollectionFactory->create()
         ->addAttributeToSelect('*')
         ->addFieldToFilter('status', 'pending'); //Add condition if you wish
     
     return $collection;
     
    }
}      
