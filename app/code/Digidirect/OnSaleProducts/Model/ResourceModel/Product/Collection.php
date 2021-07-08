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
        $catalogRuleCollection = $catalogRule->create()->getCollection()->setOrder('created_in', 'DESC');
        $catalogRuleCollection->addIsActiveFilter(1); //filter for active rules only

        $catalogRuleCollection->setCurPage(1); //filter for active rules only

        $limit = 0;

        foreach ($catalogRuleCollection as $catalogRule) {
            $productIdsAccToRule = $catalogRule->getMatchingProductIds();

            if ($limit == 15) {
                break;
            }

            foreach ($productIdsAccToRule as $productId => $ruleProductArray) {
                if (!empty($ruleProductArray[$websiteId])) {
                    if (array_key_exists($productId, $productIdsAccToRule)) {
                        $discount_amount = $catalogRule->getData('discount_amount');

                        if ($limit == 15) {
                            break;
                        }

                        if ($discount_amount > 0) {
                            $resultProductIds[$productId] = $productId;
                            $limit++;
                        }
                    }
                }
            }
        }


        $this->getSelect()->where('e.entity_id IN (' . implode(',', $resultProductIds) .')')->group('e.entity_id')->limit(15);
        return $this;
   }
}
