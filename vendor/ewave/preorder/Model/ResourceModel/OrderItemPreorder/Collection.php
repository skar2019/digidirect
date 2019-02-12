<?php

namespace Ewave\PreOrder\Model\ResourceModel\OrderItemPreorder;

use Ewave\PreOrder\Model\OrderItemPreorder as OrderItemPreorderModel;
use Ewave\PreOrder\Model\ResourceModel\OrderItemPreorder as OrderItemPreorderResourceModel;

/**
 * Class Collection
 *
 * @package Ewave\PreOrder\Model\ResourceModel\OrderItemPreorder
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
