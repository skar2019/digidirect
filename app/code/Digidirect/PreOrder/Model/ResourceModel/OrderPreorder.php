<?php

namespace Digidirect\PreOrder\Model\ResourceModel;

use Digidirect\PreOrder\Model\OrderPreorder as OrderPreorderModel;

/**
 * Class OrderPreorder
 *
 * @package Digidirect\PreOrder\Model\ResourceModel
 */
class OrderPreorder extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE = 'digidirect_preorder_order_preorder';

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
