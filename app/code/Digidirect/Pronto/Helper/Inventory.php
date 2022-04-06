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
                        \Digidirect\CustomInventoryLog\Logger\Logger $logger)
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
        $forLogs = "";
        date_default_timezone_set('UTC');
        $newTime = strtotime('-20 minutes');
        $prontofilter = date('dmYHis', $newTime);//$now->format('dmYhis');

        //$prontofilter = '05072021000000';
        // testing

        //url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=change_enquiry&check-warehouse-change=Y&date-time-change-min='.$prontofilter.'&check-price-change=Y&start-item='.$startitem;
        //live - port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=change_enquiry&check-warehouse-change=Y&date-time-change-min='.$prontofilter.'&check-price-change=Y&include-stock-movements=Y&check-price-change=Y&start-item='.$startitem;
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

        $json = $this->jsonSerializer->unserialize($result);


        if(isset($json['response']) && ($json['response']['status'] == 'FAIL'))
        {
            $msg =  $json['response']['message'];
            $this->logger->error('Pronto Inventory Sync', array('info' => $msg));
            exit;
        }

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
                $forLogs .= "SKU - ".$sku."\n";

                //pricing
                try
                {
                    $prod = $this->productRepository->get($sku);
                    if(isset($prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax']))
                    {
                        $retail = $prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax'];
                        $prod->setPrice($retail);
                        $forLogs .= "Price - ".$retail."\n";

                    }

                    if($prodRes['stk-condition-code'] == 'O')
                    {
                        $prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                    }
                    else
                    {
                        //web flag
                        //if blank, set to disable
                        if($prodRes['stk-user-only-alpha4-1'] == '')
                        {
                            $prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                        }
                        else if($prodRes['stk-user-only-alpha4-1'] == 'N')
                        {
                            $prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                        }
                        else {
                            //$prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                        }

                    }
                    //check stk-user-only-alpha4-1 if pre order "P" or awaiting stock "A"
                    if($prodRes['stk-user-only-alpha4-1'] == 'A')
                    {
                        $prod->setCustomAttribute('awaiting_product', '1');
                        $forLogs .= "Awaiting 1 \n";
                    }
                    else {
                        $prod->setCustomAttribute('awaiting_product', '0');
                        $forLogs .= "Awaiting 0 \n";
                    }

                    if($prodRes['stk-abc-class'] == 'P')
                    {
                        $prod->setCustomAttribute('pre_order', '1');
                        $prod->setCustomAttribute('preorder', '1');
                        $forLogs .= "Pre Order 1 \n";
                        //echo "pre_order 1  <br/>";
                    }

                    $totalwrhs = 0;
                    if(isset($prodRes['warehouse']['whse']))
                    {
                        foreach ($prodRes['warehouse']['whse'] as $qt)
                        {
                            if(is_array($qt))
                            {
                                $sourceItem = $this->sourceItemFactory->create();
                                $sourceItem->setSourceCode($qt['code']);
                                $sourceItem->setSku($prodRes['code']);
                                $sourceItem->setStatus(1);
                                $sourceItem->setQuantity($qt['qty_available']);
                                $totalwrhs = $totalwrhs + $qt['qty_available'];
                                $forLogs .= $qt['code']." - ".$qt['qty_available']."\n";
                                $this->sourceItemsSaveInterface->execute([$sourceItem]);
                            }
                            else
                            {
                                // to handle single warehouse
                                $sourceItem = $this->sourceItemFactory->create();
                                $sourceItem->setSourceCode($prodRes['warehouse']['whse']['code']);
                                $sourceItem->setSku($prodRes['code']);
                                $sourceItem->setStatus(1);
                                $sourceItem->setQuantity($prodRes['warehouse']['whse']['qty_available']);
                                $totalwrhs = $totalwrhs + $qt['qty_available'];
                                $forLogs .= $prodRes['warehouse']['whse']['code']." - ".$prodRes['warehouse']['whse']['qty_available']."\n";
                                $this->sourceItemsSaveInterface->execute([$sourceItem]);
                            }
                        }
                    }

                    if($prodRes['stk-condition-code'] == 'T' && $totalwrhs == 0)
                    {
                        $prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                    }

                    $this->productRepository->save($prod);
                }
                catch (\Magento\Framework\Exception\NoSuchEntityException $e)
                {
                    $forLogs .= "SKU not exist - ".$sku."\n";
                }
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
                $forLogs .= "SKU - ".$sku."\n";
                //pricing
                try {

                    $prod = $this->productRepository->get($sku);
                    if(isset($prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax']))
                    {
                        $retail = $prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax'];
                        $prod->setPrice($retail);
                        $forLogs .= "Price - ".$retail."\n";
                        $this->productRepository->save($prod);
                    }

                    if(isset($prodRes['warehouse']['whse']))
                    {
                        foreach ($prodRes['warehouse']['whse'] as $qt)
                        {
                            if(is_array($qt))
                            {
                                $sourceItem = $this->sourceItemFactory->create();
                                $sourceItem->setSourceCode($qt['code']);
                                $sourceItem->setSku($prodRes['code']);
                                $sourceItem->setStatus(1);
                                $sourceItem->setQuantity($qt['qty_available']);
                                $forLogs .= $qt['code']." - ".$qt['qty_available']."\n";
                                $this->sourceItemsSaveInterface->execute([$sourceItem]);
                            }
                            else
                            {
                                // to handle single warehouse
                                $sourceItem = $this->sourceItemFactory->create();
                                $sourceItem->setSourceCode($prodRes['warehouse']['whse']['code']);
                                $sourceItem->setSku($prodRes['code']);
                                $sourceItem->setStatus(1);
                                $sourceItem->setQuantity($prodRes['warehouse']['whse']['qty_available']);
                                $forLogs .= $prodRes['warehouse']['whse']['code']." - ".$prodRes['warehouse']['whse']['qty_available']."\n";
                                $this->sourceItemsSaveInterface->execute([$sourceItem]);
                            }
                        }
                    }
                    //echo $lastCode."<br>";
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $forLogs .= "SKU not exist - ".$sku."\n";
                }
            }
        }

        $this->logger->info($forLogs);

        if($startitem == $lastCode)
        {
            return true;
        }

        $this->enquireInventory($lastCode);
    }

    public function getSourceItemBySku($sku)
    {
        return $this->sourceItemsBySku->execute($sku);
    }

    public function enquireInventoryTest($args = 0)
    {

        if(isset($args))
        {
            $startitem = $args;
        }
        else
        {
            $startitem = 0;
        }


        $lastCode = 0;
        $forLogs = "";
        date_default_timezone_set('UTC');
        $newTime = strtotime('-20 minutes');
        $prontofilter = date('dmYHis', $newTime);//$now->format('dmYhis');

        //$prontofilter = '05072021000000';
        // testing

        //url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=change_enquiry&check-warehouse-change=Y&date-time-change-min='.$prontofilter.'&check-price-change=Y&start-item='.$startitem;
        //live - port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=change_enquiry&check-warehouse-change=Y&date-time-change-min='.$prontofilter.'&check-price-change=Y&include-stock-movements=Y&check-price-change=Y&start-item='.$startitem;
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

        if(isset($json['response']) && ($json['response']['status'] == 'FAIL'))
        {
            $msg =  $json['response']['message'];
            $this->logger->error('Pronto Inventory Sync', array('info' => $msg));
            exit;
        }

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
                $forLogs .= "SKU - ".$sku."\n";
                //pricing
                try {
                    $prod = $this->productRepository->get($sku);
                    if(isset($prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax']))
                    {
                        $retail = $prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax'];
                        $prod->setPrice($retail);
                        $forLogs .= "Price - ".$retail."\n";

                    }
                    $endis = 'nochange';
                    echo $prodRes['stk-condition-code']. " stk-condition-code <br />";
                    if($prodRes['stk-condition-code'] == 'O')
                    {
                        $prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                        $endis = 'disabled';
                    }
                    else
                    {
                        //web flag
                        //if blank, set to disable
                        if($prodRes['stk-user-only-alpha4-1'] == '')
                        {
                            $prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                            $endis = 'disabled';
                        }
                        else if($prod['stk-user-only-alpha4-1'] == 'W') //enable this since this is a inventory sync, new products wont be in this sync (hopefully)
                        {
                                $prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                            $endis = 'enabled';
                        }
                        else if($prod['stk-user-only-alpha4-1'] == 'N')
                        {
                            $prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                            $endis = 'disabled';
                        }
                        else {
                            //$prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                        }
                    }
                    //web flag
                    //if blank, set to disable
                    echo $prodRes['stk-user-only-alpha4-1']. " stk-user-only-alpha4-1 <br />";
//                    if($prodRes['stk-user-only-alpha4-1'] == '')
//                    {
//                        $prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                        $endis = 'disabled';
//                    }
//                    else if($prodRes['stk-user-only-alpha4-1'] == 'W')
//                    {
//                        $prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
//                        $endis = 'enabled';
//                    }

                    echo $endis . "<br/>";
                    $totalwrhs = 0;
                    if(isset($prodRes['warehouse']['whse']))
                    {
                        foreach ($prodRes['warehouse']['whse'] as $qt)
                        {
                            if(is_array($qt))
                            {
                                $sourceItem = $this->sourceItemFactory->create();
                                $sourceItem->setSourceCode($qt['code']);
                                $sourceItem->setSku($prodRes['code']);
                                $sourceItem->setStatus(1);
                                $sourceItem->setQuantity($qt['qty_available']);
                                $totalwrhs = $totalwrhs + $qt['qty_available'];
                                $forLogs .= $qt['code']." - ".$qt['qty_available']."\n";
                                $this->sourceItemsSaveInterface->execute([$sourceItem]);
                            }
                            else
                            {
                                // to handle single warehouse
                                $sourceItem = $this->sourceItemFactory->create();
                                $sourceItem->setSourceCode($prodRes['warehouse']['whse']['code']);
                                $sourceItem->setSku($prodRes['code']);
                                $sourceItem->setStatus(1);
                                $sourceItem->setQuantity($prodRes['warehouse']['whse']['qty_available']);
                                $totalwrhs = $totalwrhs + $qt['qty_available'];
                                $forLogs .= $prodRes['warehouse']['whse']['code']." - ".$prodRes['warehouse']['whse']['qty_available']."\n";
                                $this->sourceItemsSaveInterface->execute([$sourceItem]);
                            }
                        }
                    }

                    if($prodRes['stk-condition-code'] == 'T' && $totalwrhs == 0)
                    {
                        $prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                    }

                    $this->productRepository->save($prod);

                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $forLogs .= "SKU not exist - ".$sku."\n";
                }
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
                $forLogs .= "SKU - ".$sku."\n";
                echo $sku."<br>";
                try {

                    $prod = $this->productRepository->get($sku);
                    if(isset($prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax']))
                    {
                        $retail = $prodRes['pricing']['price-region']['prc-recommend-retail-inc-tax'];
                        $prod->setPrice($retail);
                        $forLogs .= "Price - ".$retail."\n";
                        //$this->productRepository->save($prod);
                    }

                    $endis = 'nochange';
                    echo $prodRes['stk-condition-code']. " stk-condition-code <br />";
                    if($prodRes['stk-condition-code'] == 'O')
                    {
                        $prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                        $endis = 'disabled';
                    }
                    else
                    {
                        //web flag
                        //if blank, set to disable
                        if($prodRes['stk-user-only-alpha4-1'] == '')
                        {
                            $prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                            $endis = 'disabled';
                        }
                        else if($prodRes['stk-user-only-alpha4-1'] == 'W')
                        {
                            $prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                            $endis = 'enabled';
                        }
                        else {
                            //$prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                        }

                    }

                    echo $endis . "<br/>";
                    $totalwrhs = 0;
                    if(isset($prodRes['warehouse']['whse']))
                    {
                        foreach ($prodRes['warehouse']['whse'] as $qt)
                        {
                            if(is_array($qt))
                            {
                                $sourceItem = $this->sourceItemFactory->create();
                                $sourceItem->setSourceCode($qt['code']);
                                $sourceItem->setSku($prodRes['code']);
                                $sourceItem->setStatus(1);
                                $sourceItem->setQuantity($qt['qty_available']);
                                $totalwrhs = $totalwrhs + $qt['qty_available'];
                                $forLogs .= $qt['code']." - ".$qt['qty_available']."\n";
                                $this->sourceItemsSaveInterface->execute([$sourceItem]);
                            }
                            else
                            {
                                // to handle single warehouse
                                $sourceItem = $this->sourceItemFactory->create();
                                $sourceItem->setSourceCode($prodRes['warehouse']['whse']['code']);
                                $sourceItem->setSku($prodRes['code']);
                                $sourceItem->setStatus(1);
                                $sourceItem->setQuantity($prodRes['warehouse']['whse']['qty_available']);
                                $totalwrhs = $totalwrhs + $qt['qty_available'];
                                $forLogs .= $prodRes['warehouse']['whse']['code']." - ".$prodRes['warehouse']['whse']['qty_available']."\n";
                                $this->sourceItemsSaveInterface->execute([$sourceItem]);
                            }
                        }
                    }

                    if($prodRes['stk-condition-code'] == 'T' && $totalwrhs == 0)
                    {
                        $prod->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                        echo "Set disabled dun to T and 0 SOH <br/>";
                    }

                    $this->productRepository->save($prod);

                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $forLogs .= "SKU not exist - ".$sku."\n";
                }

            }
        }

        $this->logger->info($forLogs);

        if($startitem == $lastCode)
        {
            exit;
        }

        $this->enquireInventoryTest($lastCode);
    }
}
