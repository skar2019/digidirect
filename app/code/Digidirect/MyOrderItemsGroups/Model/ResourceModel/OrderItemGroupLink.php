<?php

namespace Digidirect\MyOrderItemsGroups\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupLinkInterface;

/**
 * Class OrderItemGroup
 * @package Digidirect\MyOrderItemsGroups\Model\ResourceModel
 */
class OrderItemGroupLink extends AbstractDb
{
    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('digidirect_order_item_group_link', 'link_id');
    }

    /**
     * @param $groupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductIds($groupId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['link' => $this->getMainTable()], [])
            ->join(
                ['si' => $this->getTable('sales_order_item')],
                'si.item_id = link.sales_item_id',
                ['product_id']
            )
            ->join(
                ['sic' => $this->getTable('sales_order_item')],
                'si.item_id = sic.parent_item_id',
                ['sales_item_ids' => sprintf("GROUP_CONCAT(DISTINCT %s SEPARATOR ',')", 'sic.item_id')]
            )
            ->group('si.item_id')
            ->where('link.group_id = ?', $groupId)
            ->order('link.position ASC');

        return $connection->fetchPairs($select);
    }

    /**
     * @param $groupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSaleItemIds($groupId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['link' => $this->getMainTable()], ['sales_item_id'])
            ->where('link.group_id = ?', $groupId);

        return $connection->fetchCol($select);
    }

    /**
     * @param OrderItemCollection $collection
     * @param mixed $customerId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function joinExtraData(OrderItemCollection $collection, $customerId)
    {
        $collection->getSelect()->join(
            ['link' => $this->getMainTable()],
            'link.sales_item_id = main_table.item_id',
            []
        );

        if ($customerId) {
            $collection->getSelect()->join(
                ['group' => $this->getGroupTable()],
                'link.group_id = group.group_id',
                []
            );
            $collection->getSelect()->where('group.customer_id = ?', $customerId);
        }
        $collection->getSelect()->group('main_table.item_id');
        $collection->getSelect()->order('link.position', OrderItemCollection::SORT_ORDER_ASC);
    }

    /**
     * @return string
     */
    protected function getGroupTable()
    {
        return $this->getTable('digidirect_sales_order_item_group');
    }

    /**
     * @param OrderItemCollection $collection
     * @param $categoryId
     */
    public function filterGroups(OrderItemCollection $collection, $groupId)
    {
        $collection->getSelect()->where('link.group_id = ?',  $groupId);
    }

    /**
     * @param $salesItemId
     * @param $groupId
     * @param $position
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function insertLink($salesItemId, $groupId, $position)
    {
        $this->getConnection()->insertOnDuplicate(
            $this->getMainTable(),
            [
                OrderItemGroupLinkInterface::GROUP_ID => $groupId,
                OrderItemGroupLinkInterface::SALES_ITEM_ID => $salesItemId,
                OrderItemGroupLinkInterface::POSITION => (int)$position
            ]
        );
    }

    /**
     * @param $groupId
     * @param array $itemIds
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteLinks($groupId, array $itemIds)
    {
        $connection = $this->getConnection();
        $expr = $connection->quoteInto('group_id = ?', $groupId);
        $connection = $this->getConnection();
        if (!empty($itemIds)) {
            $expr = implode(' AND ', [
                $expr,
                $connection->quoteInto('sales_item_id IN (?)', $itemIds)
            ]);
        }
        $select = $connection->delete(
            $this->getMainTable(),
            $expr
        );
    }

    /**
     * @param $groupId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMinPosition($groupId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['link' => $this->getMainTable()], 'MIN(position)')
            ->where('link.group_id = ?', $groupId);
        return (int)$connection->fetchOne($select);
    }

    /**
     * @param $groupId
     * @param $itemId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkRelation($groupId, $itemId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['link' => $this->getMainTable()], ['link_id'])
            ->where('link.sales_item_id = ?', $itemId)
            ->where('link.group_id = ?', $groupId);
        return (int)$connection->fetchOne($select);
    }

    /**
     * @param $customerId
     * @param $direction
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function shiftItems($groupId)
    {
        $connection = $this->getConnection();
        $select = $this->getShiftSelect($groupId);
        $select->columns(new \Zend_Db_Expr('link.position+1'));
        $sql = $select->insertFromSelect(
            $this->getMainTable(),
            ['link_id', 'group_id', 'sales_item_id', 'position'],
            true
        );
        $connection->query($sql);
    }

    /**
     * @param $groupId
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getShiftSelect($groupId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['link' => $this->getMainTable()], ['link_id', 'group_id', 'sales_item_id'])
            ->where('link.group_id = ?', $groupId);
        return $select;
    }
}
