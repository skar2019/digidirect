<?php

namespace Digidirect\MyOrderItemsGroups\Model\ResourceModel\OrderItemGroup;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * OrderItemGroup collection
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
            \Digidirect\MyOrderItemsGroups\Model\OrderItemGroup::class,
            \Digidirect\MyOrderItemsGroups\Model\ResourceModel\OrderItemGroup::class
        );
    }
}
