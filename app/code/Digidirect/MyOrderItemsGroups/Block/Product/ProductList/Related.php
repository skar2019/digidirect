<?php

namespace Digidirect\MyOrderItemsGroups\Block\Product\ProductList;

use Magento\Catalog\Block\Product\ProductList\Related as RelatedParent;

class Related extends RelatedParent 
{
    /**
     * Const for product key
     */
    const KEY_PRODUCT = 'group_product';

    /**
     * @return $this
     */
    protected function _prepareData()
    {
        $product = $this->_coreRegistry->registry(self::KEY_PRODUCT);
        /* @var $product \Magento\Catalog\Model\Product */

        $this->_itemCollection = $product->getRelatedProductCollection()->addAttributeToSelect(
            'required_options'
        )->setPositionOrder()->addStoreFilter();

        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        $this->_itemCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }
}
