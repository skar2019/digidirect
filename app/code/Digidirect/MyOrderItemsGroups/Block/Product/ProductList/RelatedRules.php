<?php

namespace Digidirect\MyOrderItemsGroups\Block\Product\ProductList;

use Magento\TargetRule\Block\Catalog\Product\ProductList\Related as TargetRuleRelated;

class RelatedRules extends TargetRuleRelated
{
    /**
     * Const for product key
     */
    const KEY_PRODUCT = 'group_product';

    /**
     * Retrieve current product instance (if actual and available)
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry(self::KEY_PRODUCT);
    }
}
