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

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{

  public function getOnSaleProduct()
  {
      ini_set('max_execution_time', 300);
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
       $catalogRuleCollection->setPageSize(6);

       $limit = 0;

       foreach ($catalogRuleCollection as $catalogRule) {
           if ($limit == 15) {
               break;
           }

           $productIdsAccToRule = $catalogRule->getMatchingProductIds();

           foreach ($productIdsAccToRule as $productId => $ruleProductArray) {
               if ($limit == 15) {
                   break;
               }

               if (!empty($ruleProductArray[$websiteId])) {
                   if (array_key_exists($productId, $productIdsAccToRule)) {

                       $resultProductIds[$productId] = $productId;
                       $limit++;
                   }
               }
           }
       }

       if(!empty($resultProductIds)){
           $this->getSelect()->orderRand()->where('e.entity_id IN (' . implode(',', $resultProductIds) .')')->group('e.entity_id')->limit(15);
           return $this;
       }
       else{
           return false;
       }
  }
}
