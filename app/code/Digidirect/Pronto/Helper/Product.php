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
//                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
//                $product->setName($prodname);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                $forLogs .= "Stock Condition ".$prod['stk-condition-code']."\n";
                $endis = "Enabled = 0";
                if($prod['stk-condition-code'] == 'O')
                {
                    $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);

                }
                else
                {
                    //web flag
                    //if blank, set to disable
                    if($prod['stk-user-only-alpha4-1'] == '')
                    {
                        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                    }
                    else if($prod['stk-user-only-alpha4-1'] == 'N')
                    {
                        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                    }
                    else {
                        //$product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                        $endis = "Enabled = 1";
                    }

                }
                $forLogs .= $endis."\n";
                //check stk-user-only-alpha4-1 if pre order "P" or awaiting stock "A"
                if($prod['stk-user-only-alpha4-1'] == 'A')
                {
                    //$product->setData('awaiting_product', '1');
                    $product->setCustomAttribute('awaiting_product', '1');
                    $awaiting = "Awaiting Product = 1";
                }
                else {
                    //$product->setData('awaiting_product', '0');
                    $product->setCustomAttribute('awaiting_product', '0');
                    $awaiting = "Awaiting Product = 0";
                }

                $forLogs .= $awaiting."\n";
                //set to pre order
                if($prod['stk-abc-class'] == 'P')
                {
                    //$product->setData('awaiting_product', '1');
                    $product->setCustomAttribute('pre_order', '1');
                    $product->setCustomAttribute('preorder', '1');
                    $forLogs .= "Pre Order 1 \n";
                    //echo "pre_order 1  <br/>";
                }

                //set brands
                $brandName = strtolower($prod['stk-brand-desc']);
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
                        //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                        $product->setCategoryIds($categoryIds);
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

                //disable first. this might be causing issue on sync
                $sourceItem = $this->sourceItemFactory->create();
                $sourceItem->setSourceCode('default');
                $sourceItem->setSku($prod['code']);
                $sourceItem->setStatus(1);
                $sourceItem->setQuantity(0);
                $forLogs .="default - 0 \n";
                $this->sourceItemsSaveInterface->execute([$sourceItem]);

                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                $this->productRepository->save($product);



            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){

                //insert new product
                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
                $prodname = trim($prodname," ");
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
                $brandName = strtolower($prod['stk-brand-desc']);
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
                $toUrl = $prodname."-".$prod['code'];
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);

                // set gtin and apn
                $barcode1 = "";
                $barcode2 = "";
                $barcode3 = "";
                $barcode4 = "";
                if(isset($prod['gtins']['gtin'])) {
                    //set barcode
                    if (count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE)) {
                        $forLogs .= "barcode1 ".$prod['gtins']['gtin']['id']."\n";
                        $barcode1 = $prod['gtins']['gtin']['id'];

                    } else {
                        $x = 1;
                        foreach ($prod['gtins']['gtin'] as $gtin) {
                            switch ($x)
                            {
                                case 1:
                                    $barcode1 = $gtin['id'];
                                    break;
                                case 2:
                                    $barcode2 = $gtin['id'];
                                    break;
                                case 3:
                                    $barcode3 = $gtin['id'];
                                    break;
                                case 4:
                                    $barcode4 = $gtin['id'];
                                    break;
                                default:

                            }
                            $x++;
                        }
                    }
                }

                $product->setCustomAttribute('barcode1',$barcode1);
                $product->setCustomAttribute('barcode2',$barcode2);
                $product->setCustomAttribute('barcode3',$barcode3);
                $product->setCustomAttribute('barcode4',$barcode4);
                $forLogs .= "barcode1 ".$barcode1."\n";
                $forLogs .= "barcode2 ".$barcode2."\n";
                $forLogs .= "barcode3 ".$barcode3."\n";
                $forLogs .= "barcode4 ".$barcode4."\n";

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

                //disable first. this might be causing issue on sync
//                $sourceItem = $this->sourceItemFactory->create();
//                $sourceItem->setSourceCode('default');
//                $sourceItem->setSku($prod['code']);
//                $sourceItem->setStatus(1);
//                $sourceItem->setQuantity(0);
//                $forLogs .="default - 0 \n";
//                $this->sourceItemsSaveInterface->execute([$sourceItem]);

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
//                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
//                $product->setName($prodname);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);
                $forLogs .= "Stock Condition ".$prod['stk-condition-code']."\n";
                $endis = "Enabled = 0";
                if($prod['stk-condition-code'] == 'O')
                {
                    $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);

                }
                else
                {
                    //web flag
                    //if blank, set to disable
                    if($prod['stk-user-only-alpha4-1'] == '')
                    {
                        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                    }
                    else if($prod['stk-user-only-alpha4-1'] == 'N')
                    {
                        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                    }
                    else {
//                        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
//                        $endis = "Enabled = 1";
                    }

                }
                $forLogs .= $endis."\n";
                //check stk-user-only-alpha4-1 if pre order "P" or awaiting stock "A"
                if($prod['stk-user-only-alpha4-1'] == 'A')
                {
                    //$product->setData('awaiting_product', '1');
                    $product->setCustomAttribute('awaiting_product', '1');
                    $awaiting = "Awaiting Product = 1";
                }
                else {
                    //$product->setData('awaiting_product', '0');
                    $product->setCustomAttribute('awaiting_product', '0');
                    $awaiting = "Awaiting Product = 0";
                }
                $forLogs .= $awaiting."\n";
                //set to pre order
                if($prod['stk-abc-class'] == 'P')
                {
                    //$product->setData('awaiting_product', '1');
                    $product->setCustomAttribute('pre_order', '1');
                    $product->setCustomAttribute('preorder', '1');
                    $forLogs .= "Pre Order 1 \n";
                    //echo "pre_order 1  <br/>";
                }

                //set brands
                $brandName = strtolower($prod['stk-brand-desc']);
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

                        $forLogs .= "Categories: ".$catList."\n";
                        //echo "update categories: ".$catList."<br />";
                        //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                        $product->setCategoryIds($categoryIds);
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

                //disable first. this might be causing issue on sync
//                $sourceItem = $this->sourceItemFactory->create();
//                $sourceItem->setSourceCode('default');
//                $sourceItem->setSku($prod['code']);
//                $sourceItem->setStatus(1);
//                $sourceItem->setQuantity(0);
//                $forLogs .="default - 0 \n";
//                $this->sourceItemsSaveInterface->execute([$sourceItem]);

                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);

                $this->productRepository->save($product);
                //echo "update ".$lastCode ."<br/>";


            } catch (\Magento\Framework\Exception\NoSuchEntityException $e){

                //insert new product
                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
                $prodname = trim($prodname," ");
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
                $brandName = strtolower($prod['stk-brand-desc']);
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
                $toUrl = $prodname."-".$prod['code'];
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);

                // set gtin and apn
                $barcode1 = "";
                $barcode2 = "";
                $barcode3 = "";
                $barcode4 = "";
                if(isset($prod['gtins']['gtin'])) {
                    //set barcode
                    if (count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE)) {
                        $forLogs .= "barcode1 ".$prod['gtins']['gtin']['id']."\n";
                        $barcode1 = $prod['gtins']['gtin']['id'];

                    } else {
                        $x = 1;
                        foreach ($prod['gtins']['gtin'] as $gtin)
                        {
                            switch ($x)
                            {
                                case 1:
                                    $barcode1 = $gtin['id'];
                                    break;
                                case 2:
                                    $barcode2 = $gtin['id'];
                                    break;
                                case 3:
                                    $barcode3 = $gtin['id'];
                                    break;
                                case 4:
                                    $barcode4 = $gtin['id'];
                                    break;
                                default:

                            }
                            $x++;
                        }
                    }
                }
                //work around to set
                $product->setCustomAttribute('barcode1',$barcode1);
                $product->setCustomAttribute('barcode2',$barcode2);
                $product->setCustomAttribute('barcode3',$barcode3);
                $product->setCustomAttribute('barcode4',$barcode4);
                $forLogs .= "barcode1 ".$barcode1."\n";
                $forLogs .= "barcode2 ".$barcode2."\n";
                $forLogs .= "barcode3 ".$barcode3."\n";
                $forLogs .= "barcode4 ".$barcode4."\n";

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

//                $sourceItem = $this->sourceItemFactory->create();
//                $sourceItem->setSourceCode('default');
//                $sourceItem->setSku($prod['code']);
//                $sourceItem->setStatus(1);
//                $sourceItem->setQuantity(0);
//                $forLogs .="default - 0 \n";
//                $this->sourceItemsSaveInterface->execute([$sourceItem]);

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

    public function productProntoBulk($startItem, $endItem)
    {

        set_time_limit(300);
        $lastCode = 0;
        $forLogs = "";
        $this->attributeOptions = $this->getOptionHash('brand');

        $parentID = 2; // default category
        $getCategoryList = $this->getSubCategoryByParentID($parentID);

        //$this->logger->info('Pronto Product Sync - start item: '.$startItem);
        //echo 'Pronto Product Sync - start item: '.$startItem."<br/>";
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

        $json = $this->jsonSerializer->unserialize($result);

        $count = 0;
        foreach ($json['stockmaster']['stockcode'] as $prod)
        {

            if(!isset($prod['code']))
            {
                exit;
            }

            $lastCode = $prod['code'];

            try
            {
                //product update
                $forLogs .= "SKU ".$prod['code']."\n";
                echo "SKU ".$prod['code']."\n";
                $product = $this->productRepository->get($prod['code']);
                //set name, price, stock status
//                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
//                $product->setName($prodname);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);

                $endis = "nochange";
                echo $prod['stk-user-only-alpha4-1']." <br>";
                if($prod['stk-condition-code'] == 'O')
                {
                    $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                    $endis = 'disabled';
                }
                else
                {
                    //web flag
                    //if blank, set to disable
                    if($prod['stk-user-only-alpha4-1'] == '')
                    {
                        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                        $endis = 'disabled';
                    }
//                    else if($prod['stk-user-only-alpha4-1'] == 'W')
//                    {
//                        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
//                        $endis = 'enabled';
//                    }
                    else if($prod['stk-user-only-alpha4-1'] == 'N')
                    {
                        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                    }
                    else {
                        //$product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                        $endis = 'as is';
                    }

                }

                echo $endis." <br/>";

                if($prod['stk-user-only-alpha4-1'] == 'A')
                {
                    //$product->setData('awaiting_product', '1');
                    $product->setCustomAttribute('awaiting_product', '1');
                    echo "awaiting 1  <br/>";
                }
                else {
                    //$product->setData('awaiting_product', '0');
                    $product->setCustomAttribute('awaiting_product', '0');
                    echo "awaiting 0  <br/>";
                }

                if($prod['stk-abc-class'] == 'P')
                {
                    //$product->setData('awaiting_product', '1');
                    $product->setCustomAttribute('pre_order', '1');
                    $product->setCustomAttribute('preorder', '1');
                    echo "pre_order 1  <br/>";
                }
                //set brands
                $brandName = strtolower($prod['stk-brand-desc']);
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

                                if($category['name'] == $prod['web-category4'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." : ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                        }
                    }
                    echo $catList."<br>";
                    $forLogs .= $catList."\n";
                    if (count($categoryIds)) {

                        $forLogs .= "Categories: ".$catList."\n";
                        //echo "update categories: ".$catList."<br />";
                        //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                        $product->setCategoryIds($categoryIds);
                    }
                }

                //set apn and gtin
//                $barcode1 = "";
//                $barcode2 = "";
//                $barcode3 = "";
//                $barcode4 = "";
//                if(isset($prod['gtins']['gtin'])) {
//                    //set barcode
//                    if (count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE)) {
//                        $barcode1 = $prod['gtins']['gtin']['id'];
//
//                    } else {
//                        $x = 1;
//                        foreach ($prod['gtins']['gtin'] as $gtin) {
//                            switch ($x)
//                            {
//                                case 1:
//                                    $barcode1 = $gtin['id'];
//                                    break;
//                                case 2:
//                                    $barcode2 = $gtin['id'];
//                                    break;
//                                case 3:
//                                    $barcode3 = $gtin['id'];
//                                    break;
//                                case 4:
//                                    $barcode4 = $gtin['id'];
//                                    break;
//                                default:
//
//                            }
//                            $x++;
//                        }
//                    }
//                }
//                //work around to set
//                $product->setCustomAttribute('barcode1',$barcode1);
//                $product->setCustomAttribute('barcode2',$barcode2);
//                $product->setCustomAttribute('barcode3',$barcode3);
//                $product->setCustomAttribute('barcode4',$barcode4);
//                $forLogs .= "barcode1 ".$barcode1."\n";
//                $forLogs .= "barcode2 ".$barcode2."\n";
//                $forLogs .= "barcode3 ".$barcode3."\n";
//                $forLogs .= "barcode4 ".$barcode4."\n";

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

                $sourceItem = $this->sourceItemFactory->create();
                $sourceItem->setSourceCode('default');
                $sourceItem->setSku($prod['code']);
                $sourceItem->setStatus(1);
                $sourceItem->setQuantity(0);
                $forLogs .="default - 0 \n";
                $this->sourceItemsSaveInterface->execute([$sourceItem]);

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
                $brandName = strtolower($prod['stk-brand-desc']);
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
                $toUrl = $prodname."-".$prod['code'];
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);

                // set gtin and apn
                $barcode1 = "";
                $barcode2 = "";
                $barcode3 = "";
                $barcode4 = "";
                if(isset($prod['gtins']['gtin'])) {
                    //set barcode
                    if (count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE)) {
                        $barcode1 = $prod['gtins']['gtin']['id'];

                    } else {
                        $x = 1;
                        foreach ($prod['gtins']['gtin'] as $gtin)
                        {
                            switch ($x)
                            {
                                case 1:
                                    $barcode1 = $gtin['id'];
                                    break;
                                case 2:
                                    $barcode2 = $gtin['id'];
                                    break;
                                case 3:
                                    $barcode3 = $gtin['id'];
                                    break;
                                case 4:
                                    $barcode4 = $gtin['id'];
                                    break;
                                default:

                            }
                            $x++;
                        }
                    }
                }
                //work around to set
                $product->setCustomAttribute('barcode1',$barcode1);
                $product->setCustomAttribute('barcode2',$barcode2);
                $product->setCustomAttribute('barcode3',$barcode3);
                $product->setCustomAttribute('barcode4',$barcode4);
                $forLogs .= "barcode1 ".$barcode1."\n";
                $forLogs .= "barcode2 ".$barcode2."\n";
                $forLogs .= "barcode3 ".$barcode3."\n";
                $forLogs .= "barcode4 ".$barcode4."\n";

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

//                $sourceItem = $this->sourceItemFactory->create();
//                $sourceItem->setSourceCode('default');
//                $sourceItem->setSku($prod['code']);
//                $sourceItem->setStatus(1);
//                $sourceItem->setQuantity(0);
//                $forLogs .="default - 0 \n";
//                $this->sourceItemsSaveInterface->execute([$sourceItem]);

                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $this->productRepository->save($product);

            }
        }
        $this->logger->info($forLogs);
        return true;
    }


    public function productProntoSingle($startItem)
    {
        set_time_limit(300);

        $this->attributeOptions = $this->getOptionHash('brand');
        $lastCode = 0;
        $forLogs = "";
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

            try {

                $forLogs .= "SKU ".$prod['code']."\n";
                $product = $this->productRepository->get($prod['code']);
                $prodname = $prod['desc1']. " ".$prod['desc2']. " ".$prod['desc3'];
                $product->setName($prodname);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setStockStatus($prod['stk-stock-status']);


                //set brands
                $brandName = strtolower($prod['stk-brand-desc']);
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

                                if($category['name'] == $prod['web-category4'])
                                {
                                    $catList .=$category['name'] . " - " .$category['id']." : ";
                                    $categoryIds[] = $category['id'];
                                }
                            }
                        }
                    }
                    echo $catList."<br>";
                    $forLogs .= $catList."\n";
                    if (count($categoryIds)) {

                        $forLogs .= "Categories: ".$catList."\n";
                        //echo "update categories: ".$catList."<br />";
                        //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                        $product->setCategoryIds($categoryIds);
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
                $brandName = strtolower($prod['stk-brand-desc']);
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

                echo $catList."<br>";
                if (count($categoryIds)) {
                    $forLogs .= "Categories: ".$catList."\n";
                    //$this->categoryLinkManagement->assignProductToCategories($prod['code'], $categoryIds);
                    $product->setCategoryIds($categoryIds);
                }

                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $toUrl = $prodname."-".$prod['code'];
                $url = preg_replace('#[^0-9a-z]+#i', '-', $toUrl);
                $url = strtolower($url);
                $product->setUrlKey($url);

                // set gtin and apn
                $barcode1 = "";
                $barcode2 = "";
                $barcode3 = "";
                $barcode4 = "";
                if(isset($prod['gtins']['gtin'])) {
                    //set barcode
                    if (count($prod['gtins']['gtin']) == count($prod['gtins']['gtin'], COUNT_RECURSIVE)) {
                        $barcode1 = $prod['gtins']['gtin']['id'];

                    } else {
                        $x = 1;
                        foreach ($prod['gtins']['gtin'] as $gtin)
                        {
                            switch ($x)
                            {
                                case 1:
                                    $barcode1 = $gtin['id'];
                                    break;
                                case 2:
                                    $barcode2 = $gtin['id'];
                                    break;
                                case 3:
                                    $barcode3 = $gtin['id'];
                                    break;
                                case 4:
                                    $barcode4 = $gtin['id'];
                                    break;
                                default:

                            }
                            $x++;
                        }
                    }
                }
                //work around to set
                $product->setCustomAttribute('barcode1',$barcode1);
                $product->setCustomAttribute('barcode2',$barcode2);
                $product->setCustomAttribute('barcode3',$barcode3);
                $product->setCustomAttribute('barcode4',$barcode4);
                $forLogs .= "barcode1 ".$barcode1."\n";
                $forLogs .= "barcode2 ".$barcode2."\n";
                $forLogs .= "barcode3 ".$barcode3."\n";
                $forLogs .= "barcode4 ".$barcode4."\n";

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
        $this->logger->info($forLogs);
        return true;
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
                                if (count($sub2category->getChildrenData())) {
                                    $getSubCategoryLevelDownAgain4 = $this->getCategoryData($sub2category->getId());
                                    foreach ($getSubCategoryLevelDownAgain4->getChildrenData() as $sub3category) {
                                        $categoryData[$sub3category->getId()]  = [
                                            'name'=> $sub3category->getName(),
                                            'url'=> $sub3category->getUrl(),
                                            'id'=> $sub3category->getId()
                                        ];
                                    }
                                }
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
