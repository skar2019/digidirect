<?php

namespace Digidirect\Pronto\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Psr\Log\LoggerInterface;

class Inventory extends AbstractHelper
{
 
    /**
    * @var Curl
    */
    protected $curl;

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
                        LoggerInterface $logger) 
                    {
                        $this->curl = $curl;
                        $this->jsonSerializer = $jsonSerializer;
                        $this->sourceItemsBySku = $sourceItemsBySku;
                        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
                        $this->sourceItemFactory = $sourceItemFactory;
                        $this->logger = $logger;

    }

    public function enquireInventory() {
 
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/tec.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Inverntory Sync');
        //date today
        $now = new \DateTime();
        $prontofilter = $now->format('dmY'.'000000');
        // testing$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=change_enquiry&check-warehouse-change=Y&date-time-change-min='.$prontofilter.'&check-price-change=Y';
        //live - port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=change_enquiry&check-warehouse-change=Y&date-time-change-min='.$prontofilter.'&check-price-change=Y';
        $username = 'clint.mercado';
        $password = '849cd5080faff5ce';
        $jsonData = '{}';
        
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("Accept", "application/json");
        $this->curl->addHeader("compcode", "DIG"); //live
        //$this->curl->addHeader("compcode", "UA1"); //test
        $this->curl->addHeader("user", "ewaveapi");
        $this->curl->addHeader("token", "904241bdbf10efa9");
        // get method
        $this->curl->get($url);

        $result = $this->curl->getBody();
        // echo $result;
        $json = $this->jsonSerializer->unserialize($result);

        if(isset($json['response']) && ($json['response']['status'] == 'FAIL'))
        {
            $msg =  $json['response']['message'];
            $this->logger->error('Pronto Inventory Sync', array('info' => $msg));
            $logger->info('Inverntory Error '.$msg);
        }
        //var_dump($json['stockmaster']['stockcode']['warehouse']);
        foreach ($json['stockmaster']['stockcode'] as $prodRes)
        {
            $sku =  $prodRes['code'];
            //echo "<br />SKU : ". $sku;
            //pricing
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
            $product = $objectManager->create('\Magento\Catalog\Model\Product');

            $prod = $product->loadByAttribute('sku', $sku);
            //echo "<br /> Pronto Retail Price: " .$retail;
            //echo "<br /> Magento Price : ". $prod->getPrice() ."<br />";
            if(isset($prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax']))
            {
                $retail = $prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax'];
                $prod->setPrice($retail);
                $prod->save();
            }
            
            foreach ($prodRes['warehouse']['whse'] as $qt)
            {
                //echo "<br />".$qt['code']." - " .$qt['qty_available'];
                $sourceItem = $this->sourceItemFactory->create();
                $sourceItem->setSourceCode($qt['code']);
                $sourceItem->setSku($sku);
                $sourceItem->setStatus(1);
                $sourceItem->setQuantity($qt['qty_available']);
                $this->sourceItemsSaveInterface->execute([$sourceItem]);
            }
            
            $this->logger->info('Pronto Inventory Sync', array('inventory' => $sku));
            $logger->info('Pronto Inventory Sync '.$sku);
        }

    }   
    
    public function getSourceItemBySku($sku)
    {
        return $this->sourceItemsBySku->execute($sku);
    }
}      
