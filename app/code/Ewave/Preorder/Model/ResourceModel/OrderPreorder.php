<?php

namespace Ewave\PreOrder\Model\ResourceModel;

use Ewave\PreOrder\Model\OrderPreorder as OrderPreorderModel;

/**
 * Class OrderPreorder
 *
 * @package Ewave\PreOrder\Model\ResourceModel
 */
class OrderPreorder extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE = 'ewave_preorder_order_preorder';

    /**
     * Get id by order id
     *
     * @param int $orderId
     * @return string
     */
    public function getIdByOrderId($orderId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getMainTable(), [$this->getIdFieldName()])
            ->where(OrderPreorderModel::ORDER_ID . ' = ?', $orderId);
        $id = $connection->fetchOne($select);
        return $id;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::TABLE, OrderPreorderModel::ID);
    }
}
