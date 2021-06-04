<?php

namespace Ewave\Feed\Controller\Adminhtml\Template;

use Ewave\Feed\Controller\Adminhtml\Template;

class NewAction extends Template
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}
