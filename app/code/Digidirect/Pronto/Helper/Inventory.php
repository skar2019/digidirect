<?php

namespace Digidirect\Pronto\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;


class Inventory extends AbstractHelper
{
 
    /**
    * @var Curl
    */
    protected $curl;

    public function __construct(
                        Curl $curl,
                        JsonSerializer $jsonSerializer,
                        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku,
                        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSaveInterface,
                        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemFactory) 
                    {
                        $this->curl = $curl;
                        $this->jsonSerializer = $jsonSerializer;
                        $this->sourceItemsBySku = $sourceItemsBySku;
                        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
                        $this->sourceItemFactory = $sourceItemFactory;

    }

    public function enquireInventory() {
 
        //date today
        $now = new \DateTime();
        $prontofilter = $now->format('dmY'.'000000');
        $url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=change_enquiry&check-warehouse-change=Y&date-time-change-min='.$prontofilter.'&check-price-change=Y';
        $username = 'clint.mercado';
        $password = '849cd5080faff5ce';
        $jsonData = '{}';
        
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("Accept", "application/json");
        $this->curl->addHeader("compcode", "UA1");
        $this->curl->addHeader("user", "clint.mercado");
        $this->curl->addHeader("token", "849cd5080faff5ce");
        // get method
        $this->curl->get($url);

        $result = $this->curl->getBody();
        // echo $result;
        $json = $this->jsonSerializer->unserialize($result);

        if(isset($json['response']) && ($json['response']['status'] == 'FAIL'))
        {
            echo $json['response']['message'];
            exit;
        }
        //var_dump($json['stockmaster']['stockcode']['warehouse']);
        foreach ($json['stockmaster']['stockcode'] as $prodRes)
        {

            $retail = $prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax'];
            $sku =  $prodRes['code'];
            echo "<br />SKU : ". $sku;
            //pricing
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
            $product = $objectManager->create('\Magento\Catalog\Model\Product');

            $prod = $product->loadByAttribute('sku', $sku);
            echo "<br /> Pronto Retail Price: " .$retail;
            echo "<br /> Magento Price : ". $prod->getPrice() ."<br />";

            $prod->setPrice($retail);
            $prod->save();
            //end pricing

            //quantity by source
            echo "<br />Quantity before update <br />";
            $sourceItemList = $this->getSourceItemBySku($sku);
            foreach ($sourceItemList as $source) {
                $var = $source->getData();
                echo "<br />".$var['source_code'] . " => ". $var['quantity'];
            }

            //loop from
            foreach ($prodRes['warehouse']['whse'] as $qt)
            {
                echo "<br />".$qt['code']." - " .$qt['qty_available'];
                $sourceItem = $this->sourceItemFactory->create();
                $sourceItem->setSourceCode($qt['code']);
                $sourceItem->setSku($sku);
                $sourceItem->setStatus(1);
                $sourceItem->setQuantity($qt['qty_available']);
                $this->sourceItemsSaveInterface->execute([$sourceItem]);
            }
            echo "<br /><br /> Quantity after update <br />";
            $sourceItemList2 = $this->getSourceItemBySku($sku);
            foreach ($sourceItemList2 as $source) {
                $var = $source->getData();
                echo "<br />".$var['source_code'] . " => ". $var['quantity'];
            }
        }

    }   
    
    public function getSourceItemBySku($sku)
    {
        return $this->sourceItemsBySku->execute($sku);
    }
}      
