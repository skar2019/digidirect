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

    public function fullEnquiry() {
 
        //$url = 'https://digi-pronto.abtonline.com.au:8083/rest/abtws/login';
        $startitem = 131000;
        $limit = 500;
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
                echo "Insert here- " .$prod['code'] . "<br />";
                $prodname = $prod['desc1']. " ".$prod['desc2'];
                $product = $this->productFactory->create();
                $product->setSku($prod['code']);
                $product->setName($prodname);
                $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
                $product->setVisibility(4);
                $product->setPrice($prod['pricing']['price-region']['prc-recommend-retail-inc-tax']);
                $product->setAttributeSetId(4); // Default attribute set for products
                $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
//                // If desired, you can set a tax class like so:
//                //$product->setCustomAttribute('tax_class_id', $taxClassId);
                $product->setCustomAttribute('apn', $prod['stk-apn-number']);
                $product->setCustomAttribute('qff_base', $prod['qff-base-points-per-dollar']);
                $product->setCustomAttribute('qff_bonus_points', $prod['qff-bonus-points-per-dollar']);
                $product = $this->productRepository->save($product);
                
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
