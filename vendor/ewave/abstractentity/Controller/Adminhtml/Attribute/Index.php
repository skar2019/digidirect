<?php
namespace Ewave\AbstractEntity\Controller\Adminhtml\Attribute;

use Ewave\AbstractEntity\Controller\Adminhtml\Attribute as AttributeController;

class Index extends AttributeController
{
    /**
     * Attributes grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Abstract Entity Attributes'));
        $this->_view->renderLayout();
    }
}
