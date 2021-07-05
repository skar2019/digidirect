<?php

namespace Digidirect\Pronto\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;


class Product extends AbstractHelper
{
 
    /**
    * @var Curl
    */
    protected $curl;
    protected $productRepository;
    
    public function __construct(
                        Curl $curl,
                        JsonSerializer $jsonSerializer,
                        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku,
                        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSaveInterface,
                        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemFactory,
                        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
                        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory,
                        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
                    ){
                        $this->curl = $curl;
                        $this->jsonSerializer = $jsonSerializer;
                        $this->sourceItemsBySku = $sourceItemsBySku;
                        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
                        $this->sourceItemFactory = $sourceItemFactory;
                        $this->productRepository = $productRepository;
                        $this->productFactory = $productFactory;
                        $this->stockRegistry = $stockRegistry;

    }

    public function productEnquiry() {
 
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/login';
        $startitem = 100000; //100425 started
        $limit = 5000;
        $enditem = $startitem + $limit;
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem;
        $username = 'clint.mercado';
        $password = '849cd5080faff5ce';
        $jsonData = '{}';
        
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->addHeader("Accept", "application/json");
        $this->curl->addHeader("compcode", "DIG"); //live
        //$this->curl->addHeader("compcode", "UA1"); //test
        $this->curl->addHeader("user", "clint.mercado");
        $this->curl->addHeader("token", "849cd5080faff5ce");
        // get method
        $this->curl->get($url);

        $result = $this->curl->getBody();
        // echo $result;
        $json = $this->jsonSerializer->unserialize($result);

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            try {
                
                $product = $this->productRepository->get($prod['code']);
                
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                $product->setBrand($prod['stk-brand']);
                
                if(isset($prod['gtins']['gtin']))
                {
                    //set barcode
                    if(count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE))
                    {
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    }
                    else
                    {
                        $x =1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {
                            $att = 'barcode'.$x;
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                    
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);
                    echo "Updated - " .$prod['code'] . "<br />";

                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                $product->setBrand($prod['stk-brand']);
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prod['code'] .'-'.$prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                foreach ($prod['warehouse']['whse'] as $qt)
                {
                    $sourceItem = $this->sourceItemFactory->create();
                    $sourceItem->setSourceCode($qt['code']);
                    $sourceItem->setSku($prod['code']);
                    $sourceItem->setStatus(1);
                    $sourceItem->setQuantity($qt['qty_available']);
                    $this->sourceItemsSaveInterface->execute([$sourceItem]);
                }
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $product = $this->productRepository->save($product);
                echo "Insert here- " .$prod['code'] . " - ".$url."<br />";

            }
            
            
        }
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            echo $json['response']['message'];
            exit;
        }
        

    }
    
    public function productEnquiryOne() {
 
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/login';
        $startitem = 105001; //100425 started
        $limit = 5000;
        $enditem = $startitem + $limit;
        $url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            try {
                
                $product = $this->productRepository->get($prod['code']);
                
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                $product->setBrand($prod['stk-brand']);
                
                if(isset($prod['gtins']['gtin']))
                {
                    //set barcode
                    if(count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE))
                    {
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    }
                    else
                    {
                        $x =1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {
                            $att = 'barcode'.$x;
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                    
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);
                    echo "Updated - " .$prod['code'] . "<br />";
    //                
    //                $proddetail = $this->productRepository->get($prod['code']);
    //                $prodattri = $proddetail->getAttributes();
    //                
    //                foreach($prodattri as $attribute)
    //                {
    //                    echo $attribute->getName(). " - ";
    //                    if($attribute->getName() == 'category_ids' || $attribute->getName() == 'media_gallery' || $attribute->getName() == 'tier_price')
    //                    {
    //                        echo "is object <br />";
    //                    }
    //                    else 
    //                    {
    //                        echo $attribute->getAttributeCode() . " - " .$attribute->getFrontend()->getValue($proddetail). "<br />";
    //                    }
    //                    
    //                }
                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                $product->setBrand($prod['stk-brand']);
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prod['code'] .'-'.$prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                foreach ($prod['warehouse']['whse'] as $qt)
                {
                    $sourceItem = $this->sourceItemFactory->create();
                    $sourceItem->setSourceCode($qt['code']);
                    $sourceItem->setSku($prod['code']);
                    $sourceItem->setStatus(1);
                    $sourceItem->setQuantity($qt['qty_available']);
                    $this->sourceItemsSaveInterface->execute([$sourceItem]);
                }
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $product = $this->productRepository->save($product);
                echo "Insert here- " .$prod['code'] . " - ".$url."<br />";
//                $stockItem = $this->stockRegistry->getStockItemBySku($product->getSku());
//                $stockItem->setIsInStock($isInStock);
//                $stockItem->setQty($stockQty);
//                $this->stockRegistry->updateStockItemBySku($product->getSku(), $stockItem);
            }
            
            
        }
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            echo $json['response']['message'];
            exit;
        }
        

    }   
    
    public function productEnquiryTwo() {
 
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/login';
        $startitem = 110001; //100425 started
        $limit = 5000;
        $enditem = $startitem + $limit;
        $url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            try {
                
                $product = $this->productRepository->get($prod['code']);
                
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                $product->setBrand($prod['stk-brand']);
                
                if(isset($prod['gtins']['gtin']))
                {
                    //set barcode
                    if(count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE))
                    {
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    }
                    else
                    {
                        $x =1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {
                            $att = 'barcode'.$x;
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                    
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);
                    echo "Updated - " .$prod['code'] . "<br />";
    //                
    //                $proddetail = $this->productRepository->get($prod['code']);
    //                $prodattri = $proddetail->getAttributes();
    //                
    //                foreach($prodattri as $attribute)
    //                {
    //                    echo $attribute->getName(). " - ";
    //                    if($attribute->getName() == 'category_ids' || $attribute->getName() == 'media_gallery' || $attribute->getName() == 'tier_price')
    //                    {
    //                        echo "is object <br />";
    //                    }
    //                    else 
    //                    {
    //                        echo $attribute->getAttributeCode() . " - " .$attribute->getFrontend()->getValue($proddetail). "<br />";
    //                    }
    //                    
    //                }
                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                $product->setBrand($prod['stk-brand']);
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prod['code'] .'-'.$prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                foreach ($prod['warehouse']['whse'] as $qt)
                {
                    $sourceItem = $this->sourceItemFactory->create();
                    $sourceItem->setSourceCode($qt['code']);
                    $sourceItem->setSku($prod['code']);
                    $sourceItem->setStatus(1);
                    $sourceItem->setQuantity($qt['qty_available']);
                    $this->sourceItemsSaveInterface->execute([$sourceItem]);
                }
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $product = $this->productRepository->save($product);
                echo "Insert here- " .$prod['code'] . " - ".$url."<br />";
//                $stockItem = $this->stockRegistry->getStockItemBySku($product->getSku());
//                $stockItem->setIsInStock($isInStock);
//                $stockItem->setQty($stockQty);
//                $this->stockRegistry->updateStockItemBySku($product->getSku(), $stockItem);
            }
            
            
        }
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            echo $json['response']['message'];
            exit;
        }
        

    }   
    
    public function productEnquiryThree() {
 
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/login';
        $startitem = 115001; //100425 started
        $limit = 5000;
        $enditem = $startitem + $limit;
        $url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            try {
                
                $product = $this->productRepository->get($prod['code']);
                
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                $product->setBrand($prod['stk-brand']);
                
                if(isset($prod['gtins']['gtin']))
                {
                    //set barcode
                    if(count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE))
                    {
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    }
                    else
                    {
                        $x =1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {
                            $att = 'barcode'.$x;
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                    
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);
                    echo "Updated - " .$prod['code'] . "<br />";
    //                
    //                $proddetail = $this->productRepository->get($prod['code']);
    //                $prodattri = $proddetail->getAttributes();
    //                
    //                foreach($prodattri as $attribute)
    //                {
    //                    echo $attribute->getName(). " - ";
    //                    if($attribute->getName() == 'category_ids' || $attribute->getName() == 'media_gallery' || $attribute->getName() == 'tier_price')
    //                    {
    //                        echo "is object <br />";
    //                    }
    //                    else 
    //                    {
    //                        echo $attribute->getAttributeCode() . " - " .$attribute->getFrontend()->getValue($proddetail). "<br />";
    //                    }
    //                    
    //                }
                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                $product->setBrand($prod['stk-brand']);
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prod['code'] .'-'.$prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                foreach ($prod['warehouse']['whse'] as $qt)
                {
                    $sourceItem = $this->sourceItemFactory->create();
                    $sourceItem->setSourceCode($qt['code']);
                    $sourceItem->setSku($prod['code']);
                    $sourceItem->setStatus(1);
                    $sourceItem->setQuantity($qt['qty_available']);
                    $this->sourceItemsSaveInterface->execute([$sourceItem]);
                }
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $product = $this->productRepository->save($product);
                echo "Insert here- " .$prod['code'] . " - ".$url."<br />";
//                $stockItem = $this->stockRegistry->getStockItemBySku($product->getSku());
//                $stockItem->setIsInStock($isInStock);
//                $stockItem->setQty($stockQty);
//                $this->stockRegistry->updateStockItemBySku($product->getSku(), $stockItem);
            }
            
            
        }
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            echo $json['response']['message'];
            exit;
        }
        

    }   
    
    public function productEnquiryFour() {
 
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/login';
        $startitem = 120001; //100425 started
        $limit = 5000;
        $enditem = $startitem + $limit;
        $url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            try {
                
                $product = $this->productRepository->get($prod['code']);
                
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                $product->setBrand($prod['stk-brand']);
                
                if(isset($prod['gtins']['gtin']))
                {
                    //set barcode
                    if(count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE))
                    {
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    }
                    else
                    {
                        $x =1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {
                            $att = 'barcode'.$x;
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                    
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);
                    echo "Updated - " .$prod['code'] . "<br />";
    //                
    //                $proddetail = $this->productRepository->get($prod['code']);
    //                $prodattri = $proddetail->getAttributes();
    //                
    //                foreach($prodattri as $attribute)
    //                {
    //                    echo $attribute->getName(). " - ";
    //                    if($attribute->getName() == 'category_ids' || $attribute->getName() == 'media_gallery' || $attribute->getName() == 'tier_price')
    //                    {
    //                        echo "is object <br />";
    //                    }
    //                    else 
    //                    {
    //                        echo $attribute->getAttributeCode() . " - " .$attribute->getFrontend()->getValue($proddetail). "<br />";
    //                    }
    //                    
    //                }
                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                $product->setBrand($prod['stk-brand']);
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prod['code'] .'-'.$prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                foreach ($prod['warehouse']['whse'] as $qt)
                {
                    $sourceItem = $this->sourceItemFactory->create();
                    $sourceItem->setSourceCode($qt['code']);
                    $sourceItem->setSku($prod['code']);
                    $sourceItem->setStatus(1);
                    $sourceItem->setQuantity($qt['qty_available']);
                    $this->sourceItemsSaveInterface->execute([$sourceItem]);
                }
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $product = $this->productRepository->save($product);
                echo "Insert here- " .$prod['code'] . " - ".$url."<br />";
//                $stockItem = $this->stockRegistry->getStockItemBySku($product->getSku());
//                $stockItem->setIsInStock($isInStock);
//                $stockItem->setQty($stockQty);
//                $this->stockRegistry->updateStockItemBySku($product->getSku(), $stockItem);
            }
            
            
        }
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            echo $json['response']['message'];
            exit;
        }
        

    }   
    
    public function productEnquiryFive() {
 
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/login';
        $startitem = 125001; //100425 started
        $limit = 5000;
        $enditem = $startitem + $limit;
        $url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            try {
                
                $product = $this->productRepository->get($prod['code']);
                
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                $product->setBrand($prod['stk-brand']);
                
                if(isset($prod['gtins']['gtin']))
                {
                    //set barcode
                    if(count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE))
                    {
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    }
                    else
                    {
                        $x =1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {
                            $att = 'barcode'.$x;
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                    
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);
                    echo "Updated - " .$prod['code'] . "<br />";
    //                
    //                $proddetail = $this->productRepository->get($prod['code']);
    //                $prodattri = $proddetail->getAttributes();
    //                
    //                foreach($prodattri as $attribute)
    //                {
    //                    echo $attribute->getName(). " - ";
    //                    if($attribute->getName() == 'category_ids' || $attribute->getName() == 'media_gallery' || $attribute->getName() == 'tier_price')
    //                    {
    //                        echo "is object <br />";
    //                    }
    //                    else 
    //                    {
    //                        echo $attribute->getAttributeCode() . " - " .$attribute->getFrontend()->getValue($proddetail). "<br />";
    //                    }
    //                    
    //                }
                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                $product->setBrand($prod['stk-brand']);
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prod['code'] .'-'.$prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                foreach ($prod['warehouse']['whse'] as $qt)
                {
                    $sourceItem = $this->sourceItemFactory->create();
                    $sourceItem->setSourceCode($qt['code']);
                    $sourceItem->setSku($prod['code']);
                    $sourceItem->setStatus(1);
                    $sourceItem->setQuantity($qt['qty_available']);
                    $this->sourceItemsSaveInterface->execute([$sourceItem]);
                }
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $product = $this->productRepository->save($product);
                echo "Insert here- " .$prod['code'] . " - ".$url."<br />";
//                $stockItem = $this->stockRegistry->getStockItemBySku($product->getSku());
//                $stockItem->setIsInStock($isInStock);
//                $stockItem->setQty($stockQty);
//                $this->stockRegistry->updateStockItemBySku($product->getSku(), $stockItem);
            }
            
            
        }
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            echo $json['response']['message'];
            exit;
        }
        

    }   
    
    public function productEnquirySix() {
 
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/login';
        $startitem = 130001; //100425 started
        $limit = 5000;
        $enditem = $startitem + $limit;
        $url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            try {
                
                $product = $this->productRepository->get($prod['code']);
                
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                $product->setBrand($prod['stk-brand']);
                
                if(isset($prod['gtins']['gtin']))
                {
                    //set barcode
                    if(count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE))
                    {
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    }
                    else
                    {
                        $x =1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {
                            $att = 'barcode'.$x;
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                    
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);
                    echo "Updated - " .$prod['code'] . "<br />";
    //                
    //                $proddetail = $this->productRepository->get($prod['code']);
    //                $prodattri = $proddetail->getAttributes();
    //                
    //                foreach($prodattri as $attribute)
    //                {
    //                    echo $attribute->getName(). " - ";
    //                    if($attribute->getName() == 'category_ids' || $attribute->getName() == 'media_gallery' || $attribute->getName() == 'tier_price')
    //                    {
    //                        echo "is object <br />";
    //                    }
    //                    else 
    //                    {
    //                        echo $attribute->getAttributeCode() . " - " .$attribute->getFrontend()->getValue($proddetail). "<br />";
    //                    }
    //                    
    //                }
                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                $product->setBrand($prod['stk-brand']);
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prod['code'] .'-'.$prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                foreach ($prod['warehouse']['whse'] as $qt)
                {
                    $sourceItem = $this->sourceItemFactory->create();
                    $sourceItem->setSourceCode($qt['code']);
                    $sourceItem->setSku($prod['code']);
                    $sourceItem->setStatus(1);
                    $sourceItem->setQuantity($qt['qty_available']);
                    $this->sourceItemsSaveInterface->execute([$sourceItem]);
                }
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $product = $this->productRepository->save($product);
                echo "Insert here- " .$prod['code'] . " - ".$url."<br />";
//                $stockItem = $this->stockRegistry->getStockItemBySku($product->getSku());
//                $stockItem->setIsInStock($isInStock);
//                $stockItem->setQty($stockQty);
//                $this->stockRegistry->updateStockItemBySku($product->getSku(), $stockItem);
            }
            
            
        }
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            echo $json['response']['message'];
            exit;
        }
        

    }   
    
    public function productEnquirySeven() {
 
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/login';
        $startitem = 135001; //100425 started
        $limit = 5000;
        $enditem = $startitem + $limit;
        $url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            try {
                
                $product = $this->productRepository->get($prod['code']);
                
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                $product->setBrand($prod['stk-brand']);
                
                if(isset($prod['gtins']['gtin']))
                {
                    //set barcode
                    if(count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE))
                    {
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    }
                    else
                    {
                        $x =1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {
                            $att = 'barcode'.$x;
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                    
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);
                    echo "Updated - " .$prod['code'] . "<br />";
    //                
    //                $proddetail = $this->productRepository->get($prod['code']);
    //                $prodattri = $proddetail->getAttributes();
    //                
    //                foreach($prodattri as $attribute)
    //                {
    //                    echo $attribute->getName(). " - ";
    //                    if($attribute->getName() == 'category_ids' || $attribute->getName() == 'media_gallery' || $attribute->getName() == 'tier_price')
    //                    {
    //                        echo "is object <br />";
    //                    }
    //                    else 
    //                    {
    //                        echo $attribute->getAttributeCode() . " - " .$attribute->getFrontend()->getValue($proddetail). "<br />";
    //                    }
    //                    
    //                }
                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                $product->setBrand($prod['stk-brand']);
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prod['code'] .'-'.$prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                foreach ($prod['warehouse']['whse'] as $qt)
                {
                    $sourceItem = $this->sourceItemFactory->create();
                    $sourceItem->setSourceCode($qt['code']);
                    $sourceItem->setSku($prod['code']);
                    $sourceItem->setStatus(1);
                    $sourceItem->setQuantity($qt['qty_available']);
                    $this->sourceItemsSaveInterface->execute([$sourceItem]);
                }
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $product = $this->productRepository->save($product);
                echo "Insert here- " .$prod['code'] . " - ".$url."<br />";
//                $stockItem = $this->stockRegistry->getStockItemBySku($product->getSku());
//                $stockItem->setIsInStock($isInStock);
//                $stockItem->setQty($stockQty);
//                $this->stockRegistry->updateStockItemBySku($product->getSku(), $stockItem);
            }
            
            
        }
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            echo $json['response']['message'];
            exit;
        }
        

    }   
    
    public function getSourceItemBySku($sku)
    {
        return $this->sourceItemsBySku->execute($sku);
    }
}      
