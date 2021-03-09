<?php

namespace Digidirect\PreOrder\Model\ResourceModel\OrderItemPreorder;

use Digidirect\PreOrder\Model\OrderItemPreorder as OrderItemPreorderModel;
use Digidirect\PreOrder\Model\ResourceModel\OrderItemPreorder as OrderItemPreorderResourceModel;

/**
 * Class Collection
 *
 * @package Digidirect\PreOrder\Model\ResourceModel\OrderItemPreorder
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(OrderItemPreorderModel::class, OrderItemPreorderResourceModel::class);
    }
}
