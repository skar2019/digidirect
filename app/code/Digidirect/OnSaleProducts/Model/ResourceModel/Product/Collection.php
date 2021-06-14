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
        $this->getSelect()->joinLeft(
                ['catalogrule' => $this->getTable('catalogrule_product')],
                'e.entity_id = catalogrule.product_id'
        )->where('e.entity_id IS NOT NULL')->group('e.entity_id')->limit(25);
        return $this;
    }
}
