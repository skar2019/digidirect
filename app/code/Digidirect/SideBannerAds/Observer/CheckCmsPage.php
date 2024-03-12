<?php

namespace Digidirect\SideBannerAds\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckCmsPage extends \Magento\Framework\View\Element\Template implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $currentFullAction = $observer->getEvent()->getRequest()->getFullActionName();
        $cmsPages = array('cms_index_index','cms_page_view');
        
        if(in_array($currentFullAction, $cmsPages)){
            
            $html = $this->getLayout()->createBlock(
                \Magento\Cms\Block\Block::class
            )->setBlockId(
                'side-banner-ads'
            )->toHtml();
            
            return $html;
        }
    }

}
