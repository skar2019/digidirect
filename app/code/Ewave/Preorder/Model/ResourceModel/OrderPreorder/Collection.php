<?php

namespace Ewave\PreOrder\Model\ResourceModel\OrderPreorder;

use Ewave\PreOrder\Model\OrderPreorder as OrderPreorderModel;
use Ewave\PreOrder\Model\ResourceModel\OrderPreorder as OrderPreorderResourceModel;

/**
 * Class Collection
 *
 * @package Ewave\PreOrder\Model\ResourceModel\OrderPreorder
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(OrderPreorderModel::class, OrderPreorderResourceModel::class);
    }
}
