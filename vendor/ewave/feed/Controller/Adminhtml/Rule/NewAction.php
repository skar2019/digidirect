<?php

namespace Ewave\Feed\Controller\Adminhtml\Rule;

use Ewave\Feed\Controller\Adminhtml\Rule;

class NewAction extends Rule
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}
