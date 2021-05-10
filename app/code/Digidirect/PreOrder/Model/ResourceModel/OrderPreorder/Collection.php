<?php

namespace Digidirect\PreOrder\Model\ResourceModel\OrderPreorder;

use Digidirect\PreOrder\Model\OrderPreorder as OrderPreorderModel;
use Digidirect\PreOrder\Model\ResourceModel\OrderPreorder as OrderPreorderResourceModel;

/**
 * Class Collection
 *
 * @package Digidirect\PreOrder\Model\ResourceModel\OrderPreorder
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
