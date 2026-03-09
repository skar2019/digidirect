<?php
/**
 *
 * @category    Digidirect
 * @package     Digidirect_ShopByCategory
 */

namespace Digidirect\ShopByCategory\Model\ResourceModel\Product;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection{
    public function getProductCount($categoryId)
    {
        $this->addCategoriesFilter(['in' => $categoryId]);

        return $this->count();
    }
}
