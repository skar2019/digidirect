<?php

namespace Ewave\CheckoutFields\Model\ResourceModel\PdpFieldValue;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Ewave\CheckoutFields\Model\ResourceModel\PdpFieldValue
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
            \Ewave\CheckoutFields\Model\PdpFieldValue::class,
            \Ewave\CheckoutFields\Model\ResourceModel\PdpFieldValue::class
        );
    }
}
