<?php

namespace Digidirect\DigiDeals\Model\ResourceModel;

class Category extends \Magento\Catalog\Model\ResourceModel\Category
{
    public function getProductCount($category)
    {
        $collection = $category->getProductCollection();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
        $collection->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
        return intval($collection->count());
    }
}