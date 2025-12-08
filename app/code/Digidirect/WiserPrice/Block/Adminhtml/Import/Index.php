<?php
/**
 * Copyright © Digidirect. All rights reserved.
 */

namespace Digidirect\WiserPrice\Block\Adminhtml\Import;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

class Index extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Digidirect_WiserPrice::import/index.phtml';

    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('*/*/save');
    }
}
