<?php
/**
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this extension to newer
* version in the future.
*
* @category    Digidirect
* @package     Digidirect_OnSaleProducts
*/

namespace Digidirect\OnSaleProducts\Model\ResourceModel\Product;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection{
  public function getOnSaleProduct(){
   $storeManager = \Magento\Framework\App\ObjectManager::getInstance()->create(
               '\Magento\Store\Model\StoreManagerInterface'
       );
       $catalogRule = \Magento\Framework\App\ObjectManager::getInstance()->create(
               '\Magento\CatalogRule\Model\RuleFactory'
       );

       $websiteId = $storeManager->getStore()->getWebsiteId(); //current Website Id

       $resultProductIds = [];
       $catalogRuleCollection = $catalogRule->create()->getCollection();//setOrder('created_in', 'DESC');
       $catalogRuleCollection->getSelect()->orderRand();
       $catalogRuleCollection->addIsActiveFilter(1); //filter for active rules only
       $catalogRuleCollection->setCurPage(1);
       $catalogRuleCollection->setOrder('created_in', 'DESC');
       $limit = 0;

       foreach ($catalogRuleCollection as $catalogRule) {
           $ctr = 0;
           $product_collection = $catalogRule->getMatchingProductIds();

           if ($limit == 30) {
               break;
           }

           foreach ($product_collection as $key => $product_id) {

                if ($limit == 30) {
                    break;
                }

                if(!in_array($product_id, $resultProductIds)){
                    $resultProductIds[$product_id] = $product_id;
                    $limit++;
                    $ctr++;
                }
           }
       }

       if(!empty($resultProductIds)){
           $this->getSelect()->orderRand()->where('e.entity_id IN (' . implode(',', $resultProductIds) .')')->group('e.entity_id')->limit(30);
           return $this;
       }
  }
}
