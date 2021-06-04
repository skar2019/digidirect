<?php

namespace Ewave\PreOrder\Model\ResourceModel;

use Ewave\PreOrder\Model\OrderItemPreorder as OrderItemPreorderModel;

/**
 * Class OrderItemPreorder
 *
 * @package Ewave\PreOrder\Model\ResourceModel
 */
class OrderItemPreorder extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE = 'ewave_preorder_order_item_preorder';

    /**
     * Get id by order item id
     *
     * @param int $orderItemId
     * @return string
     */
    public function getIdByOrderItemId($orderItemId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getMainTable(), [$this->getIdFieldName()])
            ->where(OrderItemPreorderModel::ORDER_ITEM_ID . ' = ?', $orderItemId);
        $id = $connection->fetchOne($select);
        return $id;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::TABLE, \Ewave\PreOrder\Model\OrderItemPreorder::ID);
    }
}
