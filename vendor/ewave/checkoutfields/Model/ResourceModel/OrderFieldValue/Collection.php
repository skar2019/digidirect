<?php

namespace Ewave\CheckoutFields\Model\ResourceModel\OrderFieldValue;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Ewave\CheckoutFields\Model\ResourceModel\OrderFieldValue
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
            'Ewave\CheckoutFields\Model\OrderFieldValue',
            'Ewave\CheckoutFields\Model\ResourceModel\OrderFieldValue'
        );
    }
}
