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
            
            if ($order->getState() == 'canceled') {
                continue;
            }
            
            $orderId = $order->getIncrementId();
            
            echo "<br />orderId ".$orderId;
            $accountname = $order->getCustomerFirstname().$order->getCustomerLastname();
            $contactname = $order->getCustomerFirstname()." ".$order->getCustomerLastname();
            //check pronto if customer has an account.
            //if not, create customer account to pronto
            
            if(empty($accountname))
            {
                echo "<br> empty accountname <br>";
                continue;
            }
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
            
            $is_bank = false;
            
            $methodInst = $paymentInstance->getMethodInstance();
            echo "<br> payment - ". $paymentInstance->getMethod();
            $methodTitle = $methodInst->getTitle();
            echo "<br >method - ".$methodTitle;
            
            if ($paymentInstance->getMethod() == "banktransfer") {
                unset($data);
                $is_bank = true;
            }

            if($is_bank){
                continue;
            }
            
            $payment_reference = $paymentInstance->getLastTransId();
            echo "<br >payment_reference - ".$payment_reference;
            $payment_type = $this->getPaymentType($paymentInstance);
            $amount_tendered = $order->getBaseGrandTotal();
            $amount_tendered = round($amount_tendered, 2);
            
            echo "<br >amount_tendered - " .$amount_tendered;
            
            
            $data['sales-order']['header']['payment-details']['payment-detail']['payment-type'] = $payment_type;
            $data['sales-order']['header']['payment-details']['payment-detail']['payment-reference'] = $payment_reference;
            $data['sales-order']['header']['payment-details']['payment-detail']['amount-tendered'] = $amount_tendered;
            //exit;
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
            echo "<br>";
            if(isset($json['response']['status']) && ($json['response']['status'] == 'FAIL'))
            {
                echo $json['response']['message'];
                echo "<br>";
                
            }
            else if (isset($json['sales-orders']['response']['status']) && ($json['sales-orders']['response']['status'] == 'failed')) {
                echo $json['sales-orders']['response']['message'];
                echo "<br>";
            }
            else {
                //success
                //update order data with pronto order-no below
                //$json['sales-order']['sales-order']['order-no']
                echo "success";
                echo "<br>";
                //$order->setState("processing")->setStatus("processing");
                $pronto = $json['sales-orders']['sales-order']['order-no'];
                $order->setData('pronto_order_number',$pronto);
                $order->save();
                var_dump($json);
                exit; //for testing;
            }
            
            
            
        }
        
    }   
    
    public function getOrderCollection()
    {
       $collection = $this->_orderCollectionFactory->create()
         ->addAttributeToSelect('*')
         ->addFieldToFilter('pronto_order_number', array('null' => true)); //Add condition if you wish
     
     return $collection;
     
    }
    
    public function getPaymentType($paymentInstance){
        
        echo "<br >get payment type ". $paymentInstance->getMethod();
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
}      
