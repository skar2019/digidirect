<?php

namespace Ewave\Feed\Controller\Adminhtml\Dynamic\Category;

use Ewave\Feed\Controller\Adminhtml\Dynamic\Category;

class NewAction extends Category
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}
