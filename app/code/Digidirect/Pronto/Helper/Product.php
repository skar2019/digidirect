<?php

namespace Digidirect\Pronto\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Api\Data\CategoryTreeInterface;
use Magento\Catalog\Api\CategoryManagementInterface;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\Catalog\Api\CategoryLinkRepositoryInterface;

class Product extends AbstractHelper
{
    const BRAND_ATTRIBUTE_CODE = 'brand';
    /**
    * @var Curl
    */
    protected $curl;
    protected $productRepository;
    protected $attributeOptions = [];
    protected $rootCategoryName;
    protected $failedCategories = [];
    protected $categoryLinkManagement;
    protected $categoryLinkRepository;

    public function __construct(
                        Curl $curl,
                        JsonSerializer $jsonSerializer,
                        \Magento\InventoryApi\Api\GetSourceItemsBySkuInterface $sourceItemsBySku,
                        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemsSaveInterface,
                        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItemFactory,
                        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
                        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productFactory,
                        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
                        \Digidirect\CustomLog\Logger\Logger $logger,
                        ProductResource $productResource,
                        CategoryFactory $categoryFactory,
                        CategoryLinkManagementInterface $categoryLinkManagement,
                        CategoryLinkRepositoryInterface $categoryLinkRepository,
                        CategoryManagementInterface $categoryManagement
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
                        $this->categoryFactory = $categoryFactory;
                        $this->categoryLinkManagement = $categoryLinkManagement;
                        $this->categoryLinkRepository = $categoryLinkRepository;
                        $this->categoryManagement = $categoryManagement;

    }

    public function productPronto($startItem, $endItem)
    {

        $lastCode = 0;
        $this->attributeOptions = $this->getOptionHash('brand');

        $parentID = 2; // default category
        $getCategoryList = $this->getSubCategoryByParentID($parentID);

        $this->logger->info('Pronto Product Sync - start item: '.$startItem);
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

            try {

                //echo $this->rootCategoryName;
                //var_dump($prod);
                $this->logger->info("SKU ".$prod['code']);
                $product = $this->productRepository->get($prod['code']);

                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);

                //set brands
                $brandName = strtolower($prod['stk-brand']);

                if($brandName == "thinktank")
                {
                    $brandName = "think tank";
                }
                if($brandName == "peak")
                {
                    $brandName = "peak design";
                }
                if($brandName == "3lt")
                {
                    $brandName = "3 legged thing";
                }
                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

                //set categories
                $categoryIds = array();
                $catList = "";
                $productCategoryIds = $product->getCategoryIds();
                if(count($productCategoryIds) < 2)
                {
                    if (count($getCategoryList))
                    {
                        foreach ($getCategoryList as $id => $category)
                        {
                            if($category['name'] == $prod['web-category1'])
                            {
                                $catList .= $category['name'] . " - " .$category['id'] ." : ";
                                $categoryIds[] = $category['id'];
                            }
                            if(isset($prod['web-category2']))
                            {
                                if($category['name'] == $prod['web-category2'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." : ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category3']))
                            {
                                if($category['name'] == $prod['web-category3'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." : ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category4']))
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                if($category['name'] == $prod['web-category4'])
                                {
                                    $categoryIds[] = $category['id'];
                                }
                            }
                        }
                    }

                    if (count($categoryIds)) {

                        $this->logger->info("Category ".$catList);
                        //echo "update categories: ".$catList."<br />";
                        //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                        $product->setCategoryIds($categoryIds);
                    }
                }

                //set apn and gtin
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
                            if(is_array($qt))
                            {
                                $sourceItem = $this->sourceItemFactory->create();
                                $sourceItem->setSourceCode($qt['code']);
                                $sourceItem->setSku($prod['code']);
                                $sourceItem->setStatus(1);
                                $sourceItem->setQuantity($qt['qty_available']);
                                //echo $prod['code']." - ".$qt['code']." - ".$qt['qty_available']."<br>";
                                $this->logger->info($qt['code']." - ".$qt['qty_available']);
                                $this->sourceItemsSaveInterface->execute([$sourceItem]);
                            }
                            else
                            {
                                // to handle single warehouse
                                $sourceItem = $this->sourceItemFactory->create();
                                $sourceItem->setSourceCode($prod['warehouse']['whse']['code']);
                                $sourceItem->setSku($prod['code']);
                                $sourceItem->setStatus(1);
                                $sourceItem->setQuantity($prod['warehouse']['whse']['qty_available']);
                                //echo $prod['code']." - ".$prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']."<br>";
                                $this->logger->info($prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']);
                                $this->sourceItemsSaveInterface->execute([$sourceItem]);
                            }


                        }
                    }
                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);
                    //echo "update ".$lastCode ."<br/>";
                }

            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){

                //insert new product
                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
                //echo "Product Name: ".$prodname."<br>";
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4);
                //set brand
                $brandName = strtolower($prod['stk-brand']);
                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

                //set categories
                $categoryIds = array();
                $mainCat = 2;
                $catList = "";
                if (count($getCategoryList))
                {
                    foreach ($getCategoryList as $id => $category)
                    {
                        if($category['name'] == $prod['web-category1'])
                        {
                            $catList .= $category['name'] . " - " .$category['id']." : ";
                            $categoryIds[] = $category['id'];
                            $mainCat = $category['id'];
                        }
                        if(isset($prod['web-category2']))
                        {
                            if($category['name'] == $prod['web-category2'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                        if(isset($prod['web-category3']))
                        {
                            if($category['name'] == $prod['web-category3'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                        if(isset($prod['web-category4']))
                        {

                            if($category['name'] == $prod['web-category4'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                    }
                }


                if (count($categoryIds)) {
                    $this->logger->info("categories: ".$catList);
                    //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                    $product->setCategoryIds($categoryIds);
                }

                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);

                // set gtin and apn
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
                }

                if(isset($prod['warehouse']['whse']))
                {

                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        if(is_array($qt))
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            //echo $prod['code']." - ".$qt['code']." - ".$qt['qty_available']."<br>";
                            $this->logger->info($qt['code']." - ".$qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                        else
                        {
                            // to handle single warehouse
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($prod['warehouse']['whse']['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($prod['warehouse']['whse']['qty_available']);
                            //echo $prod['code']." - ".$prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']."<br>";
                            $this->logger->info($prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }

                    }
                }

                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
                //echo "insert ".$lastCode ."<br/>";

            }
        }


        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            echo $json['response']['message'];

        }
        return true;

    }

    public function productSync()
    {
        $startItem = 0;
        $lastCode = 0;
        $this->attributeOptions = $this->getOptionHash('brand');
        $forLogs = "";
        $parentID = 2; // default category
        $getCategoryList = $this->getSubCategoryByParentID($parentID);

        $this->logger->info('Pronto Product Sync - start item: '.$startItem);
        //echo 'Pronto Product Sync - start item: '.$startItem."<br/>";
        //$this->logger->info('Pronto Product Sync - start item: '.$startItem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startItem;//.$startitem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startItem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }

            $lastCode = $prod['code'];

            try {

                $forLogs .= "SKU ".$prod['code']."\n";
                $product = $this->productRepository->get($prod['code']);

                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);

                //set brands
                $brandName = strtolower($prod['stk-brand']);
                $forLogs .= $brandName."\n";
                if($brandName == "thinktank")
                {
                    $brandName = "think tank";
                }
                if($brandName == "peak")
                {
                    $brandName = "peak design";
                }
                if($brandName == "3lt")
                {
                    $brandName = "3 legged thing";
                }
                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

                //set categories
                $categoryIds = array();
                $catList = "";
                $productCategoryIds = $product->getCategoryIds();
                if(count($productCategoryIds) < 2)
                {
                    if (count($getCategoryList))
                    {
                        foreach ($getCategoryList as $id => $category)
                        {
                            if($category['name'] == $prod['web-category1'])
                            {
                                $catList .= $category['name'] . " - " .$category['id'] ." : ";
                                $categoryIds[] = $category['id'];
                            }
                            if(isset($prod['web-category2']))
                            {
                                if($category['name'] == $prod['web-category2'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." : ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category3']))
                            {
                                if($category['name'] == $prod['web-category3'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." : ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category4']))
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                if($category['name'] == $prod['web-category4'])
                                {
                                    $categoryIds[] = $category['id'];
                                }
                            }
                        }
                    }
                    $forLogs .= $catList."\n";
                    if (count($categoryIds)) {

                        $this->logger->info("Category ".$catList);
                        //echo "update categories: ".$catList."<br />";
                        //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                        $product->setCategoryIds($categoryIds);
                    }
                }

                //set apn and gtin
                if(isset($prod['gtins']['gtin'])) {
                    //set barcode
                    if (count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE)) {
                        $forLogs .= "barcode1 ".$prod['gtins']['gtin']['id']."\n";
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    } else {
                        $x = 1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {

                            $att = 'barcode' . $x;
                            $forLogs .= $att." ".$gtin['id']."\n";
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                }
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        if(is_array($qt))
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $forLogs .= $qt['code']." - ".$qt['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                        else
                        {
                            // to handle single warehouse
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($prod['warehouse']['whse']['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($prod['warehouse']['whse']['qty_available']);
                            $forLogs .= $prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }


                    }
                }
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                $this->productRepository->save($product);



            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){

                //insert new product
                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
                $forLogs .= "Product Name: ".$prodname."\n";
                $forLogs .= "SKU: ".$prod['code']."\n";
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4);
                //set brand
                $brandName = strtolower($prod['stk-brand']);
                $forLogs .= "Brand: ".$brandName."\n";
                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

                //set categories
                $categoryIds = array();
                $mainCat = 2;
                $catList = "";
                if (count($getCategoryList))
                {
                    foreach ($getCategoryList as $id => $category)
                    {
                        if($category['name'] == $prod['web-category1'])
                        {
                            $catList .= $category['name'] . " - " .$category['id']." : ";
                            $categoryIds[] = $category['id'];
                            $mainCat = $category['id'];
                        }
                        if(isset($prod['web-category2']))
                        {
                            if($category['name'] == $prod['web-category2'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                        if(isset($prod['web-category3']))
                        {
                            if($category['name'] == $prod['web-category3'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                        if(isset($prod['web-category4']))
                        {

                            if($category['name'] == $prod['web-category4'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                    }
                }


                if (count($categoryIds)) {
                    $forLogs .= "Categories: ".$catList."\n";
                    //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                    $product->setCategoryIds($categoryIds);
                }

                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);

                // set gtin and apn
                if(isset($prod['gtins']['gtin']))
                {
                    //set barcode

                    if(count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE))
                    {
                        $forLogs .= "barcode1: ".$prod['gtins']['gtin']['id']."\n";
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    }
                    else
                    {
                        $x =1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {

                            $att = 'barcode'.$x;
                            $forLogs .= $att." ".$gtin['id']."\n";
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                }

                if(isset($prod['warehouse']['whse']))
                {

                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        if(is_array($qt))
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $forLogs .= $qt['code']." - ".$qt['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                        else
                        {
                            // to handle single warehouse
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($prod['warehouse']['whse']['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($prod['warehouse']['whse']['qty_available']);
                            $forLogs .= $prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }

                    }
                }

                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);

            }
        }

        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $forLogs .= $json['response']['message'];

        }

        $this->logger->info($forLogs);

        $this->productSyncContinue($lastCode);

    }

    public function productSyncContinue($startItem)
    {

        $lastCode = $startItem;
        $this->attributeOptions = $this->getOptionHash('brand');
        $forLogs = "";
        $parentID = 2; // default category
        $getCategoryList = $this->getSubCategoryByParentID($parentID);

        $this->logger->info('Pronto Product Sync - start item: '.$startItem);
        //echo 'Pronto Product Sync - start item: '.$startItem."<br/>";
        //$this->logger->info('Pronto Product Sync - start item: '.$startItem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startItem;//.$startitem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startItem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }

            $lastCode = $prod['code'];

            try {

                $forLogs .= "SKU ".$prod['code']."\n";
                $product = $this->productRepository->get($prod['code']);
                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
                $product->setName($prodname);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);

                //set brands
                $brandName = strtolower($prod['stk-brand']);
                $forLogs .= $brandName."\n";
                if($brandName == "thinktank")
                {
                    $brandName = "think tank";
                }
                if($brandName == "peak")
                {
                    $brandName = "peak design";
                }
                if($brandName == "3lt")
                {
                    $brandName = "3 legged thing";
                }
                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

                //set categories
                $categoryIds = array();
                $catList = "";
                $productCategoryIds = $product->getCategoryIds();
                if(count($productCategoryIds) < 2)
                {
                    if (count($getCategoryList))
                    {
                        foreach ($getCategoryList as $id => $category)
                        {
                            if($category['name'] == $prod['web-category1'])
                            {
                                $catList .= $category['name'] . " - " .$category['id'] ." : ";
                                $categoryIds[] = $category['id'];
                            }
                            if(isset($prod['web-category2']))
                            {
                                if($category['name'] == $prod['web-category2'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." : ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category3']))
                            {
                                if($category['name'] == $prod['web-category3'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." : ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category4']))
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                if($category['name'] == $prod['web-category4'])
                                {
                                    $categoryIds[] = $category['id'];
                                }
                            }
                        }
                    }
                    $forLogs .= $catList."\n";
                    if (count($categoryIds)) {

                        $this->logger->info("Category ".$catList);
                        //echo "update categories: ".$catList."<br />";
                        //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                        $product->setCategoryIds($categoryIds);
                    }
                }

                //set apn and gtin
                if(isset($prod['gtins']['gtin'])) {
                    //set barcode
                    if (count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE)) {
                        $forLogs .= "barcode1 ".$prod['gtins']['gtin']['id']."\n";
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    } else {
                        $x = 1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {

                            $att = 'barcode' . $x;
                            $forLogs .= $att." ".$gtin['id']."\n";
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                }
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        if(is_array($qt))
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $forLogs .= $qt['code']." - ".$qt['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                        else
                        {
                            // to handle single warehouse
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($prod['warehouse']['whse']['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($prod['warehouse']['whse']['qty_available']);
                            $forLogs .= $prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }


                    }
                }
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                $this->productRepository->save($product);
                //echo "update ".$lastCode ."<br/>";


            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){

                //insert new product
                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
                $forLogs .= "Product Name: ".$prodname."\n";
                $forLogs .= "SKU: ".$prod['code']."\n";
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4);
                //set brand
                $brandName = strtolower($prod['stk-brand']);
                $forLogs .= "Brand: ".$brandName."\n";
                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

                //set categories
                $categoryIds = array();
                $mainCat = 2;
                $catList = "";
                if (count($getCategoryList))
                {
                    foreach ($getCategoryList as $id => $category)
                    {
                        if($category['name'] == $prod['web-category1'])
                        {
                            $catList .= $category['name'] . " - " .$category['id']." : ";
                            $categoryIds[] = $category['id'];
                            $mainCat = $category['id'];
                        }
                        if(isset($prod['web-category2']))
                        {
                            if($category['name'] == $prod['web-category2'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                        if(isset($prod['web-category3']))
                        {
                            if($category['name'] == $prod['web-category3'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                        if(isset($prod['web-category4']))
                        {

                            if($category['name'] == $prod['web-category4'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                    }
                }


                if (count($categoryIds)) {
                    $forLogs .= "Categories: ".$catList."\n";
                    //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                    $product->setCategoryIds($categoryIds);
                }

                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);

                // set gtin and apn
                if(isset($prod['gtins']['gtin']))
                {
                    //set barcode

                    if(count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE))
                    {
                        $forLogs .= "barcode1: ".$prod['gtins']['gtin']['id']."\n";
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    }
                    else
                    {
                        $x =1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {

                            $att = 'barcode'.$x;
                            $forLogs .= $att." ".$gtin['id']."\n";
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                }

                if(isset($prod['warehouse']['whse']))
                {

                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        if(is_array($qt))
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $forLogs .= $qt['code']." - ".$qt['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                        else
                        {
                            // to handle single warehouse
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($prod['warehouse']['whse']['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($prod['warehouse']['whse']['qty_available']);
                            $forLogs .= $prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }

                    }
                }

                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);

            }
        }

        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $forLogs .= $json['response']['message'];
        }

        $this->logger->info($forLogs);

        if($startItem == $lastCode)
        {
            return true;
        }

        $this->productSyncContinue($lastCode);
    }

    public function productSyncAgain()
    {
        $startItem = 112036;
        $lastCode = $startItem;
        $this->attributeOptions = $this->getOptionHash('brand');
        $forLogs = "";
        $parentID = 2; // default category
        $getCategoryList = $this->getSubCategoryByParentID($parentID);

        $this->logger->info('Pronto Product Sync - start item: '.$startItem);
        //echo 'Pronto Product Sync - start item: '.$startItem."<br/>";
        //$this->logger->info('Pronto Product Sync - start item: '.$startItem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startItem;//.$startitem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startItem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }

            $lastCode = $prod['code'];

            try {

                $forLogs .= "SKU ".$prod['code']."\n";
                $product = $this->productRepository->get($prod['code']);
                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
                $product->setName($prodname);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);

                //set brands
                $brandName = strtolower($prod['stk-brand']);
                $forLogs .= $brandName."\n";
                if($brandName == "thinktank")
                {
                    $brandName = "think tank";
                }
                if($brandName == "peak")
                {
                    $brandName = "peak design";
                }
                if($brandName == "3lt")
                {
                    $brandName = "3 legged thing";
                }
                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

                //set categories
                $categoryIds = array();
                $catList = "";
                $productCategoryIds = $product->getCategoryIds();
                if(count($productCategoryIds) < 2)
                {
                    if (count($getCategoryList))
                    {
                        foreach ($getCategoryList as $id => $category)
                        {
                            if($category['name'] == $prod['web-category1'])
                            {
                                $catList .= $category['name'] . " - " .$category['id'] ." : ";
                                $categoryIds[] = $category['id'];
                            }
                            if(isset($prod['web-category2']))
                            {
                                if($category['name'] == $prod['web-category2'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." : ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category3']))
                            {
                                if($category['name'] == $prod['web-category3'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." : ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category4']))
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                if($category['name'] == $prod['web-category4'])
                                {
                                    $categoryIds[] = $category['id'];
                                }
                            }
                        }
                    }
                    $forLogs .= $catList."\n";
                    if (count($categoryIds)) {

                        $this->logger->info("Category ".$catList);
                        //echo "update categories: ".$catList."<br />";
                        //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                        $product->setCategoryIds($categoryIds);
                    }
                }

                //set apn and gtin
                if(isset($prod['gtins']['gtin'])) {
                    //set barcode
                    if (count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE)) {
                        $forLogs .= "barcode1 ".$prod['gtins']['gtin']['id']."\n";
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    } else {
                        $x = 1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {

                            $att = 'barcode' . $x;
                            $forLogs .= $att." ".$gtin['id']."\n";
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                }
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        if(is_array($qt))
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $forLogs .= $qt['code']." - ".$qt['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                        else
                        {
                            // to handle single warehouse
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($prod['warehouse']['whse']['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($prod['warehouse']['whse']['qty_available']);
                            $forLogs .= $prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }


                    }
                }
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                $this->productRepository->save($product);



            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){

                //insert new product
                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
                $forLogs .= "Product Name: ".$prodname."\n";
                $forLogs .= "SKU: ".$prod['code']."\n";
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4);
                //set brand
                $brandName = strtolower($prod['stk-brand']);
                $forLogs .= "Brand: ".$brandName."\n";
                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

                //set categories
                $categoryIds = array();
                $mainCat = 2;
                $catList = "";
                if (count($getCategoryList))
                {
                    foreach ($getCategoryList as $id => $category)
                    {
                        if($category['name'] == $prod['web-category1'])
                        {
                            $catList .= $category['name'] . " - " .$category['id']." : ";
                            $categoryIds[] = $category['id'];
                            $mainCat = $category['id'];
                        }
                        if(isset($prod['web-category2']))
                        {
                            if($category['name'] == $prod['web-category2'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                        if(isset($prod['web-category3']))
                        {
                            if($category['name'] == $prod['web-category3'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                        if(isset($prod['web-category4']))
                        {

                            if($category['name'] == $prod['web-category4'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                    }
                }


                if (count($categoryIds)) {
                    $forLogs .= "Categories: ".$catList."\n";
                    //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                    $product->setCategoryIds($categoryIds);
                }

                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);

                // set gtin and apn
                if(isset($prod['gtins']['gtin']))
                {
                    //set barcode

                    if(count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE))
                    {
                        $forLogs .= "barcode1: ".$prod['gtins']['gtin']['id']."\n";
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    }
                    else
                    {
                        $x =1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {

                            $att = 'barcode'.$x;
                            $forLogs .= $att." ".$gtin['id']."\n";
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                }

                if(isset($prod['warehouse']['whse']))
                {

                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        if(is_array($qt))
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $forLogs .= $qt['code']." - ".$qt['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                        else
                        {
                            // to handle single warehouse
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($prod['warehouse']['whse']['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($prod['warehouse']['whse']['qty_available']);
                            $forLogs .= $prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }

                    }
                }

                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);

            }
        }

        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $forLogs .= $json['response']['message'];

        }

        $this->logger->info($forLogs);

        $this->productSyncContinue($lastCode);

    }

    public function productSyncContinueAgain($startItem)
    {

        $lastCode = $startItem;
        $this->attributeOptions = $this->getOptionHash('brand');
        $forLogs = "";
        $parentID = 2; // default category
        $getCategoryList = $this->getSubCategoryByParentID($parentID);

        $this->logger->info('Pronto Product Sync - start item: '.$startItem);
        //echo 'Pronto Product Sync - start item: '.$startItem."<br/>";
        //$this->logger->info('Pronto Product Sync - start item: '.$startItem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startItem;//.$startitem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startItem;
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

        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }

            $lastCode = $prod['code'];

            try {

                $forLogs .= "SKU ".$prod['code']."\n";
                $product = $this->productRepository->get($prod['code']);
                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
                $product->setName($prodname);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);

                //set brands
                $brandName = strtolower($prod['stk-brand']);
                $forLogs .= $brandName."\n";
                if($brandName == "thinktank")
                {
                    $brandName = "think tank";
                }
                if($brandName == "peak")
                {
                    $brandName = "peak design";
                }
                if($brandName == "3lt")
                {
                    $brandName = "3 legged thing";
                }
                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

                //set categories
                $categoryIds = array();
                $catList = "";
                $productCategoryIds = $product->getCategoryIds();
                if(count($productCategoryIds) < 2)
                {
                    if (count($getCategoryList))
                    {
                        foreach ($getCategoryList as $id => $category)
                        {
                            if($category['name'] == $prod['web-category1'])
                            {
                                $catList .= $category['name'] . " - " .$category['id'] ." : ";
                                $categoryIds[] = $category['id'];
                            }
                            if(isset($prod['web-category2']))
                            {
                                if($category['name'] == $prod['web-category2'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." : ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category3']))
                            {
                                if($category['name'] == $prod['web-category3'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." : ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category4']))
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                if($category['name'] == $prod['web-category4'])
                                {
                                    $categoryIds[] = $category['id'];
                                }
                            }
                        }
                    }
                    $forLogs .= $catList."\n";
                    if (count($categoryIds)) {

                        $this->logger->info("Category ".$catList);
                        //echo "update categories: ".$catList."<br />";
                        //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                        $product->setCategoryIds($categoryIds);
                    }
                }

                //set apn and gtin
                if(isset($prod['gtins']['gtin'])) {
                    //set barcode
                    if (count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE)) {
                        $forLogs .= "barcode1 ".$prod['gtins']['gtin']['id']."\n";
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    } else {
                        $x = 1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {

                            $att = 'barcode' . $x;
                            $forLogs .= $att." ".$gtin['id']."\n";
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                }
                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        if(is_array($qt))
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $forLogs .= $qt['code']." - ".$qt['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                        else
                        {
                            // to handle single warehouse
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($prod['warehouse']['whse']['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($prod['warehouse']['whse']['qty_available']);
                            $forLogs .= $prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }


                    }
                }
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                $this->productRepository->save($product);
                //echo "update ".$lastCode ."<br/>";


            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){

                //insert new product
                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
                $forLogs .= "Product Name: ".$prodname."\n";
                $forLogs .= "SKU: ".$prod['code']."\n";
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4);
                //set brand
                $brandName = strtolower($prod['stk-brand']);
                $forLogs .= "Brand: ".$brandName."\n";
                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

                //set categories
                $categoryIds = array();
                $mainCat = 2;
                $catList = "";
                if (count($getCategoryList))
                {
                    foreach ($getCategoryList as $id => $category)
                    {
                        if($category['name'] == $prod['web-category1'])
                        {
                            $catList .= $category['name'] . " - " .$category['id']." : ";
                            $categoryIds[] = $category['id'];
                            $mainCat = $category['id'];
                        }
                        if(isset($prod['web-category2']))
                        {
                            if($category['name'] == $prod['web-category2'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                        if(isset($prod['web-category3']))
                        {
                            if($category['name'] == $prod['web-category3'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                        if(isset($prod['web-category4']))
                        {

                            if($category['name'] == $prod['web-category4'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                    }
                }


                if (count($categoryIds)) {
                    $forLogs .= "Categories: ".$catList."\n";
                    //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                    $product->setCategoryIds($categoryIds);
                }

                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);

                // set gtin and apn
                if(isset($prod['gtins']['gtin']))
                {
                    //set barcode

                    if(count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE))
                    {
                        $forLogs .= "barcode1: ".$prod['gtins']['gtin']['id']."\n";
                        $product->setCustomAttribute('barcode1', $prod['gtins']['gtin']['id']);
                    }
                    else
                    {
                        $x =1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {

                            $att = 'barcode'.$x;
                            $forLogs .= $att." ".$gtin['id']."\n";
                            $product->setCustomAttribute($att, $gtin['id']);
                            $x++;
                        }
                    }
                }

                if(isset($prod['warehouse']['whse']))
                {

                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        if(is_array($qt))
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            $forLogs .= $qt['code']." - ".$qt['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                        else
                        {
                            // to handle single warehouse
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($prod['warehouse']['whse']['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($prod['warehouse']['whse']['qty_available']);
                            $forLogs .= $prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']."\n";
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }

                    }
                }

                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);

            }
        }

        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $forLogs .= $json['response']['message'];
        }

        $this->logger->info($forLogs);

        if($startItem == $lastCode)
        {
            return true;
        }

        $this->productSyncContinueAgain($lastCode);
    }

    public function productProntoSet($args = 0)
    {

        if(isset($args))
        {
            $startitem = $args; //100425 started
        }
        else
        {
            $startitem = 0; //100425 started
        }
        $this->attributeOptions = $this->getOptionHash('brand');
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
                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

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

                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
                $this->logger->info('Pronto Product insert'.$prodname);
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products

                $brandName = strtolower($prod['stk-brand']);
                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

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

    public function productTestProntoSet($startItem, $counter)
    {
        //comment to redeploy
        settype($counter,"integer");
        //$this->attributeOptions = $this->getOptionHash('brand');
        $lastCode = 0;

        //$parentID = 2; // default category
        //$getCategoryList = $this->getSubCategoryByParentID($parentID);

        //$this->logger->info('Pronto Product Sync - start item: '.$startItem);
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startitem.'&end-item='.$enditem; //test
        //live port :8084
        $url = 'https://digi-pronto.abtonline.com.au:8084/rest/abtws/stock-master?call-type=full_enquiry&start-item='.$startItem;
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

        $count = 0;
        foreach ($json['stockmaster']['stockcode'] as $prod)
        {
            if(!isset($prod['code']))
            {
                exit;
            }
            $count++;
            $lastCode = $prod['code'];
            echo $count." . ".$lastCode." <br>";
            $live = false;
            if($live)
            {

                try {

                    $product = $this->productRepository->get($prod['code']);

                    $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                    $product->setStockStatus($prod['stk-stock-status']);

                    $brandName = strtolower($prod['stk-brand']);
                    if($brandName == "thinktank")
                    {
                        $brandName = "think tank";
                    }
                    if($brandName == "peak")
                    {
                        $brandName = "peak design";
                    }
                    if($brandName == "3lt")
                    {
                        $brandName = "3 legged thing";
                    }
                    if(isset($this->attributeOptions[strtolower($brandName)]))
                    {
                        $brandCode = $this->attributeOptions[strtolower($brandName)];
                        $product->setBrand($brandCode);
                    }

                    //set categories
                    $categoryIds = array();
                    $productCategoryIds = $product->getCategoryIds();
                    if(count($productCategoryIds) < 2)
                    {

                        if (count($getCategoryList)) {
                            foreach ($getCategoryList as $id => $category)
                            {
                                if($category['name'] == $prod['web-category1'])
                                {
                                    $categoryIds[] = $category['id'];
                                }
                                if(isset($prod['web-category2']))
                                {
                                    if($category['name'] == $prod['web-category2'])
                                    {
                                        $categoryIds[] = $category['id'];
                                    }
                                }
                                if(isset($prod['web-category3']))
                                {
                                    if($category['name'] == $prod['web-category3'])
                                    {
                                        $categoryIds[] = $category['id'];
                                    }
                                }
                                if(isset($prod['web-category4']))
                                {
                                    if($category['name'] == $prod['web-category4'])
                                    {
                                        $categoryIds[] = $category['id'];
                                    }
                                }
                            }
                        }

                        if (count($categoryIds)) {
                            //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                            $product->setCategoryIds($categoryIds);
                        }
                    }

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
                                if(isset($qt['code']))
                                {
                                    $sourceItem = $this->sourceItemFactory->create();
                                    $sourceItem->setSourceCode($qt['code']);
                                    $sourceItem->setSku($prod['code']);
                                    $sourceItem->setStatus(1);
                                    $sourceItem->setQuantity($qt['qty_available']);
                                    $this->sourceItemsSaveInterface->execute([$sourceItem]);
                                }

                            }
                        }


                        $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                        $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                        $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                        $this->productRepository->save($product);
                        echo "update <br/>";
                    }

                }
                catch (\Magento\Framework\Exception\NoSuchEntityException $e)
                {
                    //new product
                    $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
                    $product = $this->productFactory->create();
                    $product->setSku($prod['code']);
                    $product->setName($prodname);
                    $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                    $product->setVisibility(4);
                    $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                    $product->setAttributeSetId(4);

                    $brandName = strtolower($prod['stk-brand']);
                    if($brandName == "thinktank")
                    {
                        $brandName = "think tank";
                    }
                    if($brandName == "peak")
                    {
                        $brandName = "peak design";
                    }
                    if($brandName == "3lt")
                    {
                        $brandName = "3 legged thing";
                    }
                    if(isset($this->attributeOptions[strtolower($brandName)]))
                    {
                        $brandCode = $this->attributeOptions[strtolower($brandName)];
                        $product->setBrand($brandCode);
                    }

                    //set categories
                    $categoryIds = array();
                    $mainCat = 2;
                    if (count($getCategoryList)) {
                        foreach ($getCategoryList as $id => $category)
                        {
                            if($category['name'] == $prod['web-category1'])
                            {
                                $categoryIds[] = $category['id'];
                                $mainCat = $category['id'];
                            }
                            if(isset($prod['web-category2']))
                            {
                                if($category['name'] == $prod['web-category2'])
                                {
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category3']))
                            {
                                if($category['name'] == $prod['web-category3'])
                                {
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category4']))
                            {
                                if($category['name'] == $prod['web-category4'])
                                {
                                    $categoryIds[] = $category['id'];
                                }
                            }
                        }
                    }

                    //$product->setAttributeSetId($mainCat); // Default attribute set for products

                    if (count($categoryIds)) {
                        //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                        $product->setCategoryIds($categoryIds);
                    }

                    $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                    // If desired, you can set a tax class like so:
                    //$product->setCustomAttribute('tax_class_id', $taxClassId);
                    $toUrl = $prodname;
                    $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                    $url = strtolower($url);
                    $product->setUrlKey($url);

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
                    }

                    //set warehouse stock
                    if(isset($prod['warehouse']['whse']))
                    {
                        foreach ($prod['warehouse']['whse'] as $qt)
                        {
                            if(isset($qt['code']))
                            {
                                $sourceItem = $this->sourceItemFactory->create();
                                $sourceItem->setSourceCode($qt['code']);
                                $sourceItem->setSku($prod['code']);
                                $sourceItem->setStatus(1);
                                $sourceItem->setQuantity($qt['qty_available']);
                                $this->sourceItemsSaveInterface->execute([$sourceItem]);
                            }
                        }
                    }


                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                    $this->productRepository->save($product);
                    echo "insert <br/>";
                }
            } //end is live

            //if($count >= $counter)
            //{
            //    exit;
            //}
        }

        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            $this->logger->info('Pronto Product Sync - '.$json['response']['message']);
            exit;
        }

        exit;
    }

    public function productProntoBulk($startItem, $endItem)
    {

        set_time_limit(300);
        $lastCode = 0;
        $this->attributeOptions = $this->getOptionHash('brand');

        $parentID = 2; // default category
        $getCategoryList = $this->getSubCategoryByParentID($parentID);

        $this->logger->info('Pronto Product Sync - start item: '.$startItem);
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

            try {

                //echo $this->rootCategoryName;
                //var_dump($prod);
                $this->logger->info("SKU ".$prod['code']);
                $product = $this->productRepository->get($prod['code']);

                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);

                //set brands
                $brandName = strtolower($prod['stk-brand']);

                if($brandName == "thinktank")
                {
                    $brandName = "think tank";
                }
                if($brandName == "peak")
                {
                    $brandName = "peak design";
                }
                if($brandName == "3lt")
                {
                    $brandName = "3 legged thing";
                }
                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

                //set categories
                $catList = "";
                $categoryIds = array();
                $productCategoryIds = $product->getCategoryIds();
                if(count($productCategoryIds) < 2)
                {
                    if (count($getCategoryList))
                    {
                        foreach ($getCategoryList as $id => $category)
                        {
                            if($category['name'] == $prod['web-category1'])
                            {
                                $catList .= $category['name'] . " - " .$category['id'] ." : ";
                                $categoryIds[] = $category['id'];
                            }
                            if(isset($prod['web-category2']))
                            {
                                if($category['name'] == $prod['web-category2'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." : ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category3']))
                            {
                                if($category['name'] == $prod['web-category3'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." : ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category4']))
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                if($category['name'] == $prod['web-category4'])
                                {
                                    $categoryIds[] = $category['id'];
                                }
                            }
                        }
                    }

                    if (count($categoryIds)) {
                        $this->logger->info("Categories: ".$catList);
                        //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                        $product->setCategoryIds($categoryIds);
                    }
                }

                //set apn and gtin
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
                            if(is_array($qt))
                            {
                                $sourceItem = $this->sourceItemFactory->create();
                                $sourceItem->setSourceCode($qt['code']);
                                $sourceItem->setSku($prod['code']);
                                $sourceItem->setStatus(1);
                                $sourceItem->setQuantity($qt['qty_available']);
                                //echo $prod['code']." - ".$qt['code']." - ".$qt['qty_available']."<br>";
                                $this->logger->info($qt['code']." - ".$qt['qty_available']);
                                $this->sourceItemsSaveInterface->execute([$sourceItem]);
                            }
                            else
                            {
                                // to handle single warehouse
                                $sourceItem = $this->sourceItemFactory->create();
                                $sourceItem->setSourceCode($prod['warehouse']['whse']['code']);
                                $sourceItem->setSku($prod['code']);
                                $sourceItem->setStatus(1);
                                $sourceItem->setQuantity($prod['warehouse']['whse']['qty_available']);
                                //echo $prod['code']." - ".$prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']."<br>";
                                $this->logger->info($prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']);
                                $this->sourceItemsSaveInterface->execute([$sourceItem]);
                            }


                        }
                    }
                    $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                    $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                    $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                    $this->productRepository->save($product);
                    //echo "update ".$lastCode ."<br/>";
                }

            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){

                //insert new product
                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
                //echo "Product Name: ".$prodname."<br>";
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4);
                //set brand
                $brandName = strtolower($prod['stk-brand']);
                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

                //set categories
                $categoryIds = array();
                $mainCat = 2;
                $catList = "";
                if (count($getCategoryList))
                {
                    foreach ($getCategoryList as $id => $category)
                    {
                        if($category['name'] == $prod['web-category1'])
                        {
                            $catList .= $category['name'] . " - " .$category['id']." : ";
                            $categoryIds[] = $category['id'];
                            $mainCat = $category['id'];
                        }
                        if(isset($prod['web-category2']))
                        {
                            if($category['name'] == $prod['web-category2'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                        if(isset($prod['web-category3']))
                        {
                            if($category['name'] == $prod['web-category3'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                        if(isset($prod['web-category4']))
                        {

                            if($category['name'] == $prod['web-category4'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." : ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                    }
                }

                if (count($categoryIds)) {
                    $this->logger->info("Categories: ".$catList);
                    //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                    $product->setCategoryIds($categoryIds);
                }

                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname;
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);

                // set gtin and apn
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
                }

                if(isset($prod['warehouse']['whse']))
                {

                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        if(is_array($qt))
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            //echo $prod['code']." - ".$qt['code']." - ".$qt['qty_available']."<br>";
                            $this->logger->info($qt['code']." - ".$qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                        else
                        {
                            // to handle single warehouse
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($prod['warehouse']['whse']['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($prod['warehouse']['whse']['qty_available']);
                            //echo $prod['code']." - ".$prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']."<br>";
                            $this->logger->info($prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }


                    }
                }

                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);
                //echo "insert ".$lastCode ."<br/>";

            }
        }


        if(isset($json['response']['status']) && $json['response']['status'] == 'FAIL')
        {
            echo $json['response']['message'];

        }
        return true;
    }

    public function productProntoSingle($startItem)
    {
        set_time_limit(300);

        $this->attributeOptions = $this->getOptionHash('brand');
        $lastCode = 0;
        echo 'Pronto Product Sync - start item: '.$startItem."<br/>";
        $parentID = 2;
        $getCategoryList = $this->getSubCategoryByParentID($parentID);
        //var_dump($getCategoryList);
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
            echo " SKU ".$prod['code']."<br>";
            $this->logger->info(" SKU ".$prod['code']);
            try {

                $product = $this->productRepository->get($prod['code']);
                $brandName = strtolower($prod['stk-brand']);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);

                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

                $productCategoryIds = $product->getCategoryIds();
                if(count($productCategoryIds) < 2)
                {

                    $catList = "";
                    $categoryIds = array();
                    if (count($getCategoryList)) {
                        foreach ($getCategoryList as $id => $category)
                        {
                            if($category['name'] == $prod['web-category1'])
                            {
                                $catList .= $category['name'] . " - " .$category['id'] ." / ";
                                $categoryIds[] = $category['id'];
                            }
                            if(isset($prod['web-category2']))
                            {
                                if($category['name'] == $prod['web-category2'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." / ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category3']))
                            {
                                if($category['name'] == $prod['web-category3'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." / ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                            if(isset($prod['web-category4']))
                            {
                                if($category['name'] == $prod['web-category4'])
                                {
                                    $categoryIds[] = $category['id'];
                                }
                            }
                        }
                    }

                    if (count($categoryIds)) {
                        echo "update categories: ".$catList."<br />";
                        //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                        $product->setCategoryIds($categoryIds);
                    }
                }

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
                }

                if(isset($prod['warehouse']['whse']))
                {
                    foreach ($prod['warehouse']['whse'] as $qt)
                    {
                        if(is_array($qt))
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            echo $prod['code']." - ".$qt['code']." - ".$qt['qty_available']."<br>";
                            $this->logger->info($qt['code']." - ".$qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                        else
                        {
                            // to handle single warehouse
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($prod['warehouse']['whse']['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($prod['warehouse']['whse']['qty_available']);
                            echo $prod['code']." - ".$prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']."<br>";
                            $this->logger->info($prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                    }
                }
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                $this->productRepository->save($product);
                echo "update ".$lastCode ."<br/>";

            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                // insert your error handling here

                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
                echo $prodname . "<br>";
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4);
                $mainCat = 2;
                $categoryIds = array();
                if (count($getCategoryList))
                {
                    $catList = "";
                    foreach ($getCategoryList as $id => $category)
                    {
                        if($category['name'] == $prod['web-category1'])
                        {
                            $catList .= $category['name'] . " - " .$category['id']." / ";
                            $categoryIds[] = $category['id'];
                            $mainCat = $category['id'];
                        }
                        if(isset($prod['web-category2']))
                        {
                            if($category['name'] == $prod['web-category2'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." / ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                        if(isset($prod['web-category3']))
                        {
                            if($category['name'] == $prod['web-category3'])
                            {
                                $catList .=$category['name'] . " - " .$category['id']." / ";
                                $categoryIds[] = $category['id'];
                            }
                        }
                        if(isset($prod['web-category4']))
                        {

                            if($category['name'] == $prod['web-category4'])
                            {
                                $categoryIds[] = $category['id'];
                            }
                        }
                    }
                }
                var_dump($categoryIds);
                if (count($categoryIds)) {
                    echo "insert categories: ".$catList."<br />";
                    //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                    $product->setCategoryIds($categoryIds);

                }

                $brandName = strtolower($prod['stk-brand']);

                if(isset($this->attributeOptions[strtolower($brandName)]))
                {
                    $brandCode = $this->attributeOptions[strtolower($brandName)];
                    $product->setBrand($brandCode);
                }

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
                        if(is_array($qt))
                        {
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($qt['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($qt['qty_available']);
                            echo $prod['code']." - ".$qt['code']." - ".$qt['qty_available']."<br>";
                            $this->logger->info($qt['code']." - ".$qt['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }
                        else
                        {
                            // to handle single warehouse
                            $sourceItem = $this->sourceItemFactory->create();
                            $sourceItem->setSourceCode($prod['warehouse']['whse']['code']);
                            $sourceItem->setSku($prod['code']);
                            $sourceItem->setStatus(1);
                            $sourceItem->setQuantity($prod['warehouse']['whse']['qty_available']);
                            echo $prod['code']." - ".$prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']."<br>";
                            $this->logger->info($prod['warehouse']['whse']['code']." - ".$prod['warehouse']['whse']['qty_available']);
                            $this->sourceItemsSaveInterface->execute([$sourceItem]);
                        }


                    }
                }

                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                //$this->productRepository->save($product);
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


    public function getSubCategoryByParentID(int $categoryId): array
    {
        $categoryData = [];

        $getSubCategory = $this->getCategoryData($categoryId);
        foreach ($getSubCategory->getChildrenData() as $category) {
            $categoryData[$category->getId()] = [
                'name'=> $category->getName(),
                'url'=> $category->getUrl(),
                'id'=> $category->getId()
            ];
            if (count($category->getChildrenData())) {
                $getSubCategoryLevelDown = $this->getCategoryData($category->getId());
                foreach ($getSubCategoryLevelDown->getChildrenData() as $subcategory) {
                        $categoryData[$subcategory->getId()]  = [
                            'name'=> $subcategory->getName(),
                            'url'=> $subcategory->getUrl(),
                            'id'=> $subcategory->getId()
                        ];
                        if (count($subcategory->getChildrenData())) {
                            $getSubCategoryLevelDownAgain = $this->getCategoryData($subcategory->getId());
                            foreach ($getSubCategoryLevelDownAgain->getChildrenData() as $sub2category) {
                                    $categoryData[$sub2category->getId()]  = [
                                        'name'=> $sub2category->getName(),
                                        'url'=> $sub2category->getUrl(),
                                        'id'=> $sub2category->getId()
                                    ];
                            }
                        }
                }
            }
        }

        return $categoryData;
    }

    public function getCategoryData(int $categoryId): ?CategoryTreeInterface
    {
        try {
            $getSubCategory = $this->categoryManagement->getTree($categoryId);
        } catch (NoSuchEntityException $e) {
            $this->logger->error("Category not found", [$e]);
            $getSubCategory = null;
        }

        return $getSubCategory;
    }

}
