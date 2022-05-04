<?php
namespace Digidirect\Catalog\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class OverrideTitle implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        if ($observer->getEvent()->getData('full_action_name') === 'catalog_category_view')
        {
            $layout = $observer->getEvent()->getData('layout');
            $listBlock = $layout->getBlock('category.products.list');
            $categoryViewBlock = $layout->getBlock('category.products');
            $count  = 0;
            if ($listBlock) 
            {
                $productCollection = $listBlock->getLoadedProductCollection();
                if ($productCollection && $productCollection->count() >0)
                { 
                    $count = $productCollection->count();
                }
            }
            if ($categoryViewBlock) 
            {
                $currentCategory = $categoryViewBlock->getCurrentCategory();
                if ($currentCategory)
                {    
                    $count = $currentCategory->getProductCollection()->count();
                }
            }
            $pageMainTitle = $layout->getBlock('page.main.title');
            if ($pageMainTitle)
            {
                $oldTitle =$pageMainTitle->getPageTitle();
                $pageMainTitle->setPageTitle(
                    $oldTitle
                    . _(' - Product Count: ' . (int) $count)
                );
            }
        }
    }
}