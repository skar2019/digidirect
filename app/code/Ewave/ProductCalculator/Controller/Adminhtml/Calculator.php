<?php

namespace Ewave\ProductCalculator\Controller\Adminhtml;

/**
 * Class Calculator
 * @package Ewave\ProductCalculator\Controller\Adminhtml
 */
abstract class Calculator extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Ewave_ProductCalculator::calculator_manage';

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Ewave'), __('Ewave'))
            ->addBreadcrumb(__('Product Finder'), __('Product Finder'));
        return $resultPage;
    }
}
