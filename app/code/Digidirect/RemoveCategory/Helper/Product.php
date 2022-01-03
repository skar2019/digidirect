<?php

namespace Digidirect\RemoveCategory\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Product extends AbstractHelper
{
    
    public function removeCategory($id) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $categoryLinkRepository = $objectManager->get('\Magento\Catalog\Model\CategoryLinkRepository');
        $categoryFactory = $objectManager->get('\Magento\Catalog\Model\CategoryFactory');

        $category = $categoryFactory->create()->load($id);
        $categoryProducts = $category->getProductCollection()->addAttributeToSelect('*');
        
        foreach ($categoryProducts as $cp) {
            $categoryLinkRepository->deleteByIds($id,$cp->getSku());
        }
    }
    
}      
