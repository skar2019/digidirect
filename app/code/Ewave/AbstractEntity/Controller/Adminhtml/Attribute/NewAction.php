<?php
namespace Ewave\AbstractEntity\Controller\Adminhtml\Attribute;

use Ewave\AbstractEntity\Controller\Adminhtml\Attribute as AttributeController;

class NewAction extends AttributeController
{
    /**
     * Create new attribute action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->addActionLayoutHandles();
        $this->_forward('edit');
    }
}
