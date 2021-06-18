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
       
       $excluded_products = array(11491, 12063, 12967, 19457, 23505, 173);
       $storeManager = \Magento\Framework\App\ObjectManager::getInstance()->create(
           '\Magento\Store\Model\StoreManagerInterface'
       );
       $catalogRule = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\CatalogRule\Model\RuleFactory'
       );

       $websiteId = $storeManager->getStore()->getWebsiteId();//current Website Id

       $resultProductIds = [];
       $catalogRuleCollection = $catalogRule->create()->getCollection()->setOrder('rule_id','DSC');
       $catalogRuleCollection->addIsActiveFilter(1);//filter for active rules only
       foreach ($catalogRuleCollection as $catalogRule) {
           $productIdsAccToRule = $catalogRule->getMatchingProductIds();
           foreach ($productIdsAccToRule as $productId => $ruleProductArray) {
               if (!empty($ruleProductArray[$websiteId])) {
                   
                   if(!in_array($productId, $excluded_products)){
                       $resultProductIds[$productId] = $productId;
                   }
               }
           }
       }
       $this->getSelect()->where('e.entity_id IN (' . implode(',', $resultProductIds) .')')->group('e.entity_id')->limit(15);
       return $this;
       
   }
}