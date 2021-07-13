<?php

namespace Digidirect\Pronto\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;

class Product extends AbstractHelper
{
    const BRAND_ATTRIBUTE_CODE = 'brand';
    /**
    * @var Curl
    */
    protected $curl;
    protected $productRepository;
    protected $attributeOptions = [];
    
    public function __construct(
                        Curl $curl,
                        JsonSerializer $jsonSerializer,
                        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku,
                        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSaveInterface,
                        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemFactory,
                        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
                        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory,
                        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
                        LoggerInterface $logger,
                        ProductResource $productResource
                    ){
                        $this->curl = $curl;
                        $this->jsonSerializer = $jsonSerializer;
                        $this->sourceItemsBySku = $sourceItemsBySku;
                        $this->sourceItemsSaveInterface = $sourceItemsSaveInterface;
                        $this->sourceItemFactory = $sourceItemFactory;
                        $this->productRepository = $productRepository;
                        $this->productFactory = $productFactory;
                        $this->stockRegistry = $stockRegistry;
                        $this->logger = $logger;
                        $this->productResource = $productResource;

    }

    public function productPronto($args = 0) {
 
        if(isset($args))
        {
            $startitem = $args; //100425 started   
        }
        else 
        {
            $startitem = 0; //100425 started
        }
        $lastCode = 0;
        $this->logger->info('Pronto Product Sync - start item: '.$startitem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
                
            try {
                
                $product = $this->productRepository->get($prod['code']);
                $this->logger->info('Pronto Product update: '.$lastCode);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                    
                    if(isset($prod['warehouse']['whse']))
                    {
                        foreach ($prod['warehouse']['whse'] as $qt)
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                    }
                    

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);

                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $this->logger->info('Pronto Product insert: '.$prodname);
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
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                }
                
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
            }
            
        }
        
        $this->productProntoSet($lastCode);
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $this->logger->info('Pronto Product Sync - '.$json['response']['message']);
            exit;
        }
        

    }
    
    public function productProntoOne() {
 
        $startitem = 106156; //100425 started
        
        $lastCode = 0;
        $this->logger->info('Pronto Product Sync - start item: '.$startitem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
                
            try {
                
                $product = $this->productRepository->get($prod['code']);
                $this->logger->info('Pronto Product update: '.$lastCode);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                    
                    if(isset($prod['warehouse']['whse']))
                    {
                        foreach ($prod['warehouse']['whse'] as $qt)
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                    }
                    

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);

                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $this->logger->info('Pronto Product insert: '.$prodname);
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                }
                
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
            }
            
        }
        
        $this->productProntoSet($lastCode);
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $this->logger->info('Pronto Product Sync - '.$json['response']['message']);
            exit;
        }
        

    }
    
    public function productProntoTwo() {
 
        $startitem = 114773; //100425 started
        
        $lastCode = 0;
        $this->logger->info('Pronto Product Sync - start item: '.$startitem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
                
            try {
                
                $product = $this->productRepository->get($prod['code']);
                $this->logger->info('Pronto Product update: '.$lastCode);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                    
                    if(isset($prod['warehouse']['whse']))
                    {
                        foreach ($prod['warehouse']['whse'] as $qt)
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                    }
                    

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);

                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $this->logger->info('Pronto Product insert: '.$prodname);
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                }
                
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
            }
            
        }
        
        $this->productProntoSet($lastCode);
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $this->logger->info('Pronto Product Sync - '.$json['response']['message']);
            exit;
        }
        

    }
    
    public function productProntoThree() {
 
        $startitem = 120409; //100425 started
        
        $lastCode = 0;
        $this->logger->info('Pronto Product Sync - start item: '.$startitem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
                
            try {
                
                $product = $this->productRepository->get($prod['code']);
                $this->logger->info('Pronto Product update: '.$lastCode);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                    
                    if(isset($prod['warehouse']['whse']))
                    {
                        foreach ($prod['warehouse']['whse'] as $qt)
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                    }
                    

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);

                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $this->logger->info('Pronto Product insert: '.$prodname);
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                }
                
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
            }
            
        }
        
        $this->productProntoSet($lastCode);
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $this->logger->info('Pronto Product Sync - '.$json['response']['message']);
            exit;
        }
        

    }
    
    public function productProntoFour() {
 
        $startitem = 123432; //100425 started
        
        $lastCode = 0;
        $this->logger->info('Pronto Product Sync - start item: '.$startitem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
                
            try {
                
                $product = $this->productRepository->get($prod['code']);
                $this->logger->info('Pronto Product update: '.$lastCode);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                    
                    if(isset($prod['warehouse']['whse']))
                    {
                        foreach ($prod['warehouse']['whse'] as $qt)
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                    }
                    

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);

                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $this->logger->info('Pronto Product insert: '.$prodname);
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                }
                
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
            }
            
        }
        
        $this->productProntoSet($lastCode);
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $this->logger->info('Pronto Product Sync - '.$json['response']['message']);
            exit;
        }
        

    }
    
    public function productProntoFive() {
 
        $startitem = 126304; //100425 started
        
        $lastCode = 0;
        $this->logger->info('Pronto Product Sync - start item: '.$startitem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
                
            try {
                
                $product = $this->productRepository->get($prod['code']);
                $this->logger->info('Pronto Product update: '.$lastCode);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                    
                    if(isset($prod['warehouse']['whse']))
                    {
                        foreach ($prod['warehouse']['whse'] as $qt)
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                    }
                    

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);

                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $this->logger->info('Pronto Product insert: '.$prodname);
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                }
                
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
            }
            
        }
        
        $this->productProntoSet($lastCode);
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $this->logger->info('Pronto Product Sync - '.$json['response']['message']);
            exit;
        }
        

    }
    
    public function productProntoSix() {
 
        $startitem = 128787; //100425 started
        
        $lastCode = 0;
        $this->logger->info('Pronto Product Sync - start item: '.$startitem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
                
            try {
                
                $product = $this->productRepository->get($prod['code']);
                $this->logger->info('Pronto Product update: '.$lastCode);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                    
                    if(isset($prod['warehouse']['whse']))
                    {
                        foreach ($prod['warehouse']['whse'] as $qt)
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                    }
                    

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);

                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $this->logger->info('Pronto Product insert: '.$prodname);
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                }
                
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
            }
            
        }
        
        $this->productProntoSet($lastCode);
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $this->logger->info('Pronto Product Sync - '.$json['response']['message']);
            exit;
        }
        

    }
    
    public function productProntoSeven() {
 
        $startitem = 130469; //100425 started
        
        $lastCode = 0;
        $this->logger->info('Pronto Product Sync - start item: '.$startitem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
                
            try {
                
                $product = $this->productRepository->get($prod['code']);
                $this->logger->info('Pronto Product update: '.$lastCode);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                    
                    if(isset($prod['warehouse']['whse']))
                    {
                        foreach ($prod['warehouse']['whse'] as $qt)
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                    }
                    

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);

                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $this->logger->info('Pronto Product insert: '.$prodname);
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                }
                
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
            }
            
        }
        
        $this->productProntoSet($lastCode);
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $this->logger->info('Pronto Product Sync - '.$json['response']['message']);
            exit;
        }
        

    }
    
    public function productProntoEight() {
 
        $startitem = 132662; //100425 started
        
        $lastCode = 0;
        $this->logger->info('Pronto Product Sync - start item: '.$startitem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
                
            try {
                
                $product = $this->productRepository->get($prod['code']);
                $this->logger->info('Pronto Product update: '.$lastCode);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                    
                    if(isset($prod['warehouse']['whse']))
                    {
                        foreach ($prod['warehouse']['whse'] as $qt)
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                    }
                    

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);

                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $this->logger->info('Pronto Product insert: '.$prodname);
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                }
                
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
            }
            
        }
        
        $this->productProntoSet($lastCode);
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $this->logger->info('Pronto Product Sync - '.$json['response']['message']);
            exit;
        }
        

    }
    
    public function productProntoNine() {
 
        $startitem = 134410; //100425 started
        
        $lastCode = 0;
        $this->logger->info('Pronto Product Sync - start item: '.$startitem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
                
            try {
                
                $product = $this->productRepository->get($prod['code']);
                $this->logger->info('Pronto Product update: '.$lastCode);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                    
                    if(isset($prod['warehouse']['whse']))
                    {
                        foreach ($prod['warehouse']['whse'] as $qt)
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                    }
                    

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);

                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $this->logger->info('Pronto Product insert: '.$prodname);
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                }
                
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
            }
            
        }
        
        $this->productProntoSet($lastCode);
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $this->logger->info('Pronto Product Sync - '.$json['response']['message']);
            exit;
        }
        

    }
    
    public function productProntoTen() {
 
        $startitem = 135924; //100425 started
        
        $lastCode = 0;
        $this->logger->info('Pronto Product Sync - start item: '.$startitem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
                
            try {
                
                $product = $this->productRepository->get($prod['code']);
                $this->logger->info('Pronto Product update: '.$lastCode);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                    
                    if(isset($prod['warehouse']['whse']))
                    {
                        foreach ($prod['warehouse']['whse'] as $qt)
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                    }
                    

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);

                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $this->logger->info('Pronto Product insert: '.$prodname);
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                }
                
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
            }
            
        }
        
        $this->productProntoSet($lastCode);
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $this->logger->info('Pronto Product Sync - '.$json['response']['message']);
            exit;
        }
        

    }
    
    public function productProntoEleven() {
 
        $startitem = 137346; //100425 started
        
        $lastCode = 0;
        $this->logger->info('Pronto Product Sync - start item: '.$startitem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
                
            try {
                
                $product = $this->productRepository->get($prod['code']);
                $this->logger->info('Pronto Product update: '.$lastCode);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                    
                    if(isset($prod['warehouse']['whse']))
                    {
                        foreach ($prod['warehouse']['whse'] as $qt)
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                    }
                    

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);

                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $this->logger->info('Pronto Product insert: '.$prodname);
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                }
                
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
            }
            
        }
        
        $this->productProntoSet($lastCode);
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $this->logger->info('Pronto Product Sync - '.$json['response']['message']);
            exit;
        }
        

    }
    
    public function productProntoTwelve() {
 
        $startitem = 138630; //100425 started
        
        $lastCode = 0;
        $this->logger->info('Pronto Product Sync - start item: '.$startitem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
                
            try {
                
                $product = $this->productRepository->get($prod['code']);
                $this->logger->info('Pronto Product update: '.$lastCode);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                    
                    if(isset($prod['warehouse']['whse']))
                    {
                        foreach ($prod['warehouse']['whse'] as $qt)
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                    }
                    

                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);

                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $this->logger->info('Pronto Product insert: '.$prodname);
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                }
                
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
            }
            
        }
        
        $this->productProntoSet($lastCode);
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $this->logger->info('Pronto Product Sync - '.$json['response']['message']);
            exit;
        }
        

    }
    
    
    public function productProntoSet($args = 0) {
 
        if(isset($args))
        {
            $startitem = $args; //100425 started   
        }
        else 
        {
            $startitem = 0; //100425 started
        }
        $lastCode = 0;
        $this->logger->info('Pronto Product Sync - start item: '.$startitem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
                
            try {
                
                $product = $this->productRepository->get($prod['code']);
                $this->logger->info('Pronto Product update '.$lastCode);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                    if(isset($prod['warehouse']['whse']))
                    {
                        foreach ($prod['warehouse']['whse'] as $qt)
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                    }
                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);

                }
                
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here
                
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $this->logger->info('Pronto Product insert'.$prodname);
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);
                
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                }
                
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
            }
        }
        
        if($startitem == $lastCode)
        {
            exit;
        }
        
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $this->logger->info('Pronto Product Sync - '.$json['response']['message']);
            exit;
        }
        

    } 
    
    public function productTestProntoSet($startItem) {
 
        
        $lastCode = 0;
        echo 'Pronto Product Sync - start item: '.$startItem."<br/>";
        $this->logger->info('Pronto Product Sync - start item: '.$startItem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startItem;//.$startitem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem;
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
        //var_dump($json['stockmaster']['stockcode']);
        
        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
            
            echo $lastCode ."<br/>";
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
                     if(isset($prod['warehouse']['whse']))
                    {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                    }
                    }
                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);
                    echo "update ".$lastCode ."<br/>";
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
                $toUrl = $prodname;
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
                $this->productRepository->save($product);
                echo "insert ".$lastCode ."<br/>";
            }
        }
        
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            echo $json['response']['message'];
            exit;
        }
    } 
    
    public function productProntoBulk($startItem, $endItem) {
 
        set_time_limit(300);
        $lastCode = 0;
        echo 'Pronto Product Sync - start item: '.$startItem."<br/>";
        //$this->logger->info('Pronto Product Sync - start item: '.$startItem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startItem;//.$startitem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startItem.'&end-item='.$endItem;
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
        //var_dump($json['stockmaster']['stockcode']);
        //var_dump($json);
        
        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
            
            echo $lastCode ."<br/>";
            try {
                
                $product = $this->productRepository->get($prod['code']);

                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                    
                    if(isset($prod['warehouse']['whse']))
                    {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        echo " / WHSE CODE:".$qt['code'];
                        echo " / SKU: ".$prod['code'];
                        echo " / QTY: ".$qt['qty_available'];
                        echo "<br/>";
                    }
                    }
                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);
                    echo "update ".$lastCode ."<br/>";
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
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
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
                    echo "WHSE CODE:".$qt['code'];
                    echo " SKU: ".$prod['code'];
                    echo " QTY: ".$qt['qty_available'];
                    echo "<br/>";
                }
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
                echo "insert ".$lastCode ."<br/>";
            }
        }
        
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            echo $json['response']['message'];
            
        }
        exit;
    } 
    
    public function productProntoSingle($startItem) {
        set_time_limit(300);
        
        $lastCode = 0;
        echo 'Pronto Product Sync - start item: '.$startItem."<br/>";
        //$this->logger->info('Pronto Product Sync - start item: '.$startItem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startItem;//.$startitem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startItem.'&end-item='.$startItem;
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
        //var_dump($json['stockmaster']['stockcode']);
        //var_dump($json);
        
        foreach ($json['stockmaster'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            
            $lastCode = $prod['code'];
            
            echo $lastCode ."<br/>";
            try {
                
                $product = $this->productRepository->get($prod['code']);
                $brandName = strtolower($prod['stk-brand']);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
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
                     if(isset($prod['warehouse']['whse']))
                    {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        $sourceItem = $this->sourceItemFactory->create();
                        $sourceItem->setSourceCode($qt['code']);
                        $sourceItem->setSku($prod['code']);
                        $sourceItem->setStatus(1);
                        $sourceItem->setQuantity($qt['qty_available']);
                        $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        echo "/ WHSE CODE:".$qt['code'];
                        echo "/ SKU: ".$prod['code'];
                        echo "/ QTY: ".$qt['qty_available'];
                        echo "<br/>";
                    }
                    }
                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);
                    echo "update ".$lastCode ."<br/>";
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
                
                $brandName = strtolower($prod['stk-brand']);
                $this->attributeOptions = $this->getOptionHash('brand');
                $brandCode = $this->attributeOptions[strtolower($brandName)];
                $product->setBrand($brandCode);
                
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
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
                    echo "WHSE CODE:".$qt['code'];
                    echo " SKU: ".$prod['code'];
                    echo " QTY: ".$qt['qty_available'];
                    echo "<br/>";
                }
                    
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
                echo "insert ".$lastCode ."<br/>";
            }
        }
        
        
        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            echo $json['response']['message'];
            
        }
        exit;
    } 
    
    public function getSourceItemBySku($sku)
    {
        return $this->sourceItemsBySku->execute($sku);
    }
    
    
    protected function getOptionHash(string $attributeCode): array
    {
        $result = [];
        $attribute = $this->productResource->getAttribute($attributeCode);
        $options = $attribute->getSource()->getAllOptions(false);
        foreach ($options as $option) {
            if (!isset($option['value']) || !strlen($option['value'])) {
                continue;
            }
            $result[strtolower($option['label'])] = $option['value'];
        }
        return $result;
    }
    
    
}      
