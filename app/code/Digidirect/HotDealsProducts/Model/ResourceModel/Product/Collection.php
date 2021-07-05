<?php
/**
 *
 * @category    Digidirect
 * @package     Digidirect_HotDealsProducts
 */

namespace Digidirect\HotDealsProducts\Model\ResourceModel\Product;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection{
    public function getHotDealsProduct()
    {
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $categoryFactory = $objectManager->get('\Magento\Catalog\Model\CategoryFactory');
        $category = $categoryFactory->create()->load(767);
        $category->getProductCollection()->setPageSize(15);
        $categoryProducts = $category->getProductCollection()->addAttributeToSelect('*');
        
        return $categoryProducts;
    }
}
