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
                        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
                        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory,
                        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
                        LoggerInterface $logger)
    {
                        $this->curl = $curl;
                        $this->jsonSerializer = $jsonSerializer;
                        $this->sourceItemsBySku = $sourceItemsBySku;
                        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
                        $this->sourceItemFactory = $sourceItemFactory;
                        $this->productRepository = $productRepository;
                        $this->productFactory = $productFactory;
                        $this->stockRegistry = $stockRegistry;
                        $this->logger = $logger;
    }

    public function enquireInventory($args = 0) {
 
        if(isset($args))
        {
            $startitem = $args;
        }
        else 
        {
            $startitem = 0;
        }
        
        $lastCode = 0;
        
        $now = new \DateTime(); //date today
        $prontofilter = $now->format('d-M-Y');
        //$prontofilter = '05072021000000';
        // testing

        //url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=change_enquiry&check-warehouse-change=Y&date-time-change-min='.$prontofilter.'&check-price-change=Y&start-item='.$startitem;
        //live - port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&check-warehouse-change=Y&date-change-min='.$prontofilter.'&check-price-change=Y&start-item='.$startitem;
        $username = 'clint.mercado';
        $password = '849cd5080faff5ce';
        $jsonData = '{}';
        
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("Accept", "application/json");
        $this->curl->addHeader("compcode", "DIG"); //live
        $this->curl->addHeader("user", "ewaveapi");
        $this->curl->addHeader("token", "904241bdbf10efa9");
        
        //$this->curl->addHeader("compcode", "UA1"); //test
        //$this->curl->addHeader("user", "clint.mercado");
        //$this->curl->addHeader("token", "849cd5080faff5ce");
        // get method
        $this->curl->get($url);

        $result = $this->curl->getBody();
        // echo $result;
        $json = $this->jsonSerializer->unserialize($result);
        
        //var_dump($json);
        if(isset($json['response']) && ($json['response']['status'] == 'FAIL'))
        {
            $msg =  $json['response']['message'];
            $this->logger->error('Pronto Inventory Sync', array('info' => $msg));
            exit;
        }
        //var_dump($json['stockmaster']['stockcode']['warehouse']);
        if(isset($json['stockmaster']['stockcode']['code']))
        {
            foreach ($json['stockmaster'] as $prodRes)
            {
                if(!isset($prodRes['code']))
                {
                    exit;
                }

                $sku =  $prodRes['code'];
                $lastCode = $sku;

                //pricing
                try {
                    $prod = $this->productRepository->get($sku);
                    if(isset($prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax']))
                    {
                        $retail = $prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax'];
                        $prod->setPrice($retail);
                        $this->productRepository->save($prod);
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
                } catch (Exception $ex) {
                    $this->logger->error('Pronto Inventory Error', array('Error' => $ex->getMessage()));
                    continue;
                }
                
                

                $this->logger->info('Pronto Inventory Sync', array('inventory' => $sku));
            }
        }
        else
        {
            foreach ($json['stockmaster']['stockcode'] as $prodRes)
            {

                if(!isset($prodRes['code']))
                {
                    exit;
                }

                $sku =  $prodRes['code'];
                $lastCode = $sku;

                //pricing
                try {
                    
                    $prod = $this->productRepository->get($sku);
                    if(isset($prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax']))
                    {
                        $retail = $prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax'];
                        $prod->setPrice($retail);
                        $this->productRepository->save($prod);
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
                } catch (Exception $ex) {
                    $this->logger->error('Pronto Inventory Error', array('Error' => $ex->getMessage()));
                    continue;
                }

                $this->logger->info('Pronto Inventory Sync', array('inventory' => $sku));
            }
        }
        
        if($startitem == $lastCode)
        {
            exit;
        }

        $this->enquireInventory($lastCode);
    }   
    
    public function getSourceItemBySku($sku)
    {
        return $this->sourceItemsBySku->execute($sku);
    }
    
    public function enquireInventoryTest($args = 0) {
 
        if(isset($args))
        {
            $startitem = $args;
        }
        else 
        {
            $startitem = 0;
        }
        
        $lastCode = 0;
        
        $now = new \DateTime(); //date today
        //$prontofilter = $now->format('d-M-Y');
        $prontofilter = '10-JUL-2021';
        // testing

        //url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=change_enquiry&check-warehouse-change=Y&date-time-change-min='.$prontofilter.'&check-price-change=Y&start-item='.$startitem;
        //live - port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&check-warehouse-change=Y&date-change-min='.$prontofilter.'&check-price-change=Y&start-item='.$startitem;
        $username = 'clint.mercado';
        $password = '849cd5080faff5ce';
        $jsonData = '{}';
        
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("Accept", "application/json");
        $this->curl->addHeader("compcode", "DIG"); //live
        $this->curl->addHeader("user", "ewaveapi");
        $this->curl->addHeader("token", "904241bdbf10efa9");
        
        //$this->curl->addHeader("compcode", "UA1"); //test
        //$this->curl->addHeader("user", "clint.mercado");
        //$this->curl->addHeader("token", "849cd5080faff5ce");
        // get method
        $this->curl->get($url);

        $result = $this->curl->getBody();
        // echo $result;
        $json = $this->jsonSerializer->unserialize($result);
        
        //var_dump($json);
        if(isset($json['response']) && ($json['response']['status'] == 'FAIL'))
        {
            $msg =  $json['response']['message'];
            $this->logger->error('Pronto Inventory Sync', array('info' => $msg));
            exit;
        }
        //var_dump($json['stockmaster']['stockcode']);
        if(isset($json['stockmaster']['stockcode']['code']))
        {
            foreach ($json['stockmaster'] as $prodRes)
            {
                echo "stockmaster only";
                
                if(!isset($prodRes['code']))
                {
                    exit;
                }

                $sku =  $prodRes['code'];
                $lastCode = $sku;
                echo $lastCode."<br />";
                //pricing
                try {
                    $prod = $this->productRepository->get($sku);
                    echo "<br> old price:".$prod->getPrice();
                    if(isset($prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax']))
                    {
                        $retail = $prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax'];
                        echo "<br> pronto price:".$retail;
                        $prod->setPrice($retail);
                        $this->productRepository->save($prod);
                    }
                    echo "<br> new price:".$prod->getPrice();
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
                } catch (Exception $ex) {
                    echo $ex->getMessage();
                    $this->logger->error('Pronto Inventory Error', array('Error' => $ex->getMessage()));
                    continue;
                }
                
                

                $this->logger->info('Pronto Inventory Sync', array('inventory' => $sku));
            }
        }
        else
        {
            foreach ($json['stockmaster']['stockcode'] as $prodRes)
            {
                echo "<br /> stockmaster stockcode";
                if(!isset($prodRes['code']))
                {
                    exit;
                }

                $sku =  $prodRes['code'];
                $lastCode = $sku;
                echo "<br />".$lastCode."<br />";
                //pricing
                try {
                    
                    $prod = $this->productRepository->get($sku);
                    echo "<br> old price:".$prod->getPrice();
                    if(isset($prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax']))
                    {
                        $retail = $prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax'];
                        echo "<br> pronto price:".$prod->getPrice();
                        $prod->setPrice($retail);
                        $this->productRepository->save($prod);
                    }
                    echo "<br> new price:".$prod->getPrice();
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
                } catch (Exception $ex) {
                    echo $ex->getMessage();
                    $this->logger->error('Pronto Inventory Error', array('Error' => $ex->getMessage()));
                    continue;
                }

                $this->logger->info('Pronto Inventory Sync', array('inventory' => $sku));
            }
        }
        
        if($startitem == $lastCode)
        {
            exit;
        }

        $this->enquireInventory($lastCode);
    } 
}      
