<?php

namespace Ewave\ProductOverlay\Controller\Adminhtml\Overlays;

/**
 * Class NewAction
 * @package Ewave\ProductOverlay\Controller\Adminhtml\Overlays
 */
class NewAction extends \Ewave\ProductOverlay\Controller\Adminhtml\Overlays
{
    /**
     * Index Action.
     * Forward to Edit Action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
