<?php

namespace Ewave\ProductCalculator\Controller\Adminhtml;

/**
 * Class FieldGroupCategory
 * @package Ewave\ProductCalculator\Controller\Adminhtml
 */
abstract class FieldGroupCategory extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Ewave_ProductCalculator::field_group_category_manage';

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
            ->addBreadcrumb(__('Field Group'), __('Field Group Category'));
        return $resultPage;
    }
}
