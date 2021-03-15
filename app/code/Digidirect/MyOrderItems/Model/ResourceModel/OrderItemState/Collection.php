<?php

namespace Digidirect\MyOrderItems\Model\ResourceModel\OrderItemState;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Backup collection
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
        $this->_init(\Digidirect\MyOrderItems\Model\OrderItemState::class, \Digidirect\MyOrderItems\Model\ResourceModel\OrderItemState::class);
    }
}
