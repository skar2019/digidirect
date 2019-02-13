<?php

namespace Ewave\Blog\Controller\Adminhtml\Category\Widget;

use Magento\Backend\App\Action;

/**
 * Class Chooser
 * @package Ewave\Blog\Controller\Adminhtml\Category\Widget
 */
class Chooser extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ewave_Blog::blog';

    /**
     * Chooser Source action
     *
     * @return void
     */
    public function execute()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');

        $categoryGrid = $this->_view->getLayout()->createBlock(
            \Ewave\Blog\Block\Adminhtml\Category\Widget\Chooser::class,
            '',
            ['data' => ['id' => $uniqId]]
        );
        $html = $categoryGrid->toHtml();

        $this->getResponse()->setBody($html);
    }
}
