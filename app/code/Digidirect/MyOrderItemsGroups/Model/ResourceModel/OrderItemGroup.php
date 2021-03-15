<?php

namespace Digidirect\MyOrderItemsGroups\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;

/**
 * Class OrderItemGroup
 * @package Digidirect\MyOrderItemsGroups\Model\ResourceModel
 */
class OrderItemGroup extends AbstractDb
{
    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('digidirect_sales_order_item_group', 'group_id');
    }

    /**
     * @param $customerId
     * @param $groupId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkGroupForCustomer($customerId, $groupId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['groups' => $this->getMainTable()], 'COUNT(*)')
            ->where('groups.group_id = ?', $groupId)
            ->where('groups.customer_id = ?', $customerId);

        return (int)$connection->fetchOne($select);
    }

    /**
     * @param $customerId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCurrentGroup($customerId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['groups' => $this->getMainTable()], 'group_id')
            ->where('groups.customer_id = ?', $customerId)
            ->order('groups.updated_at DESC')
            ->limit(1);
        
        return (int)$connection->fetchOne($select);
    }
}
