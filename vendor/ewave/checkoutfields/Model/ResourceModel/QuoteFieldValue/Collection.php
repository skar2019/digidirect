<?php

namespace Ewave\CheckoutFields\Model\ResourceModel\QuoteFieldValue;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Ewave\CheckoutFields\Model\ResourceModel\QuoteFieldValue
 */
class Collection extends AbstractCollection
{
    /**
     * Initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Ewave\CheckoutFields\Model\QuoteFieldValue',
            'Ewave\CheckoutFields\Model\ResourceModel\QuoteFieldValue'
        );
    }
}
