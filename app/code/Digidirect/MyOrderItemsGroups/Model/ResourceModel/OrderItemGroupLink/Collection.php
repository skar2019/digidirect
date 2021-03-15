<?php

namespace Digidirect\MyOrderItemsGroups\Model\ResourceModel\OrderItemGroupLink;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * OrderItemGroupLink collection
 */
class Collection extends AbstractCollection
{
    /**
     * Standard collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Digidirect\MyOrderItemsGroups\Model\OrderItemGroupLink::class,
            \Digidirect\MyOrderItemsGroups\Model\ResourceModel\OrderItemGroupLink::class
        );
    }
}
