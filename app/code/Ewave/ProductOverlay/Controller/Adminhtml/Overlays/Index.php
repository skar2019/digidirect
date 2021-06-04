<?php

namespace Ewave\ProductOverlay\Controller\Adminhtml\Overlays;

/**
 * Class Index
 * @package Ewave\ProductOverlay\Controller\Adminhtml\Overlays
 */
class Index extends \Ewave\ProductOverlay\Controller\Adminhtml\Overlays
{
    /**
     * Overlay Index Action. Shows overlays list
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ewave_ProductOverlay::overlay');
        $resultPage->getConfig()->getTitle()->prepend(__('Ewave Product Overlays'));
        $resultPage->addBreadcrumb(__('Ewave'), __('Ewave'));
        $resultPage->addBreadcrumb(__('Product Overlays'), __('Product Overlays'));
        return $resultPage;
    }
}
