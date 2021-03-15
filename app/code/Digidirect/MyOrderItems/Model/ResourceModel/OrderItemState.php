<?php

namespace Digidirect\MyOrderItems\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use Digidirect\MyOrderItems\Api\Data\OrderItemStateInterface;

/**
 * Class OrderItemState
 * @package Digidirect\MyOrderItems\Model\ResourceModel
 */
class OrderItemState extends AbstractDb
{
    /**
     * Shift constants
     */
    const LEFT = 'left';
    const RIGHT = 'right';
    const MIN = 'min';
    const MAX = 'max';

    /**
     * Category name core
     */
    const CATEGORY_NAME_CODE = 'name';

    /**
     * Use is object new method for save of object
     *
     * @var bool
     */
    protected $_useIsObjectNew = true;

    /**
     * Primary key auto increment flag
     *
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('digidirect_sales_order_item_state', 'sales_item_id');
    }

    /**
     * @param $itemId
     * @param $customerId
     * @return int
     * @throws LocalizedException
     */
    public function checkItemPermission($itemId, $customerId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['state' => $this->getMainTable()], 'sales_item_id')
            ->join(
                ['si' => $this->getTable('sales_order_item')],
                'si.item_id = state.sales_item_id',
                []
            )
            ->join(
                ['so' => $this->getTable('sales_order')],
                'si.order_id = so.entity_id',
                []
            )
            ->where('so.customer_id = ?', $customerId)
            ->where('state.sales_item_id = ?', $itemId);

        return (int)$connection->fetchOne($select);
    }

    /**
     * @param $customerId
     * @param bool $enabled
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMaxSortOrder($customerId, $enabled = false)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['state' => $this->getMainTable()], 'MAX(sort_order)')
            ->join(
                ['si' => $this->getTable('sales_order_item')],
                'si.item_id = state.sales_item_id',
                []
            )
            ->join(
                ['so' => $this->getTable('sales_order')],
                'si.order_id = so.entity_id',
                []
            )
            ->where('so.customer_id = ?', $customerId);

        if ($enabled) {
            $select->where('state.status = ?', OrderItemStateInterface::ENABLED);
        }

        return (int)$connection->fetchOne($select);
    }

    /**
     * @param $customerId
     * @param $direction
     * @param array $interval
     * @throws LocalizedException
     */
    public function shiftInterval($customerId, $direction, array $interval)
    {
        $connection = $this->getConnection();
        $select = $this->getShiftSelect($customerId, $direction, $interval);
        $select->columns(new \Zend_Db_Expr($this->getShiftExpression($direction)));
        $sql = $select->insertFromSelect(
            $this->getMainTable(),
            ['sales_item_id', 'sort_order'],
            true
        );
        $connection->query($sql);
    }

    /**
     * @param $customerId
     * @param $direction
     * @param array $interval
     * @return \Magento\Framework\DB\Select
     * @throws LocalizedException
     */
    protected function getShiftSelect($customerId, $direction, array $interval)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['state' => $this->getMainTable()], ['sales_item_id'])
            ->join(
                ['si' => $this->getTable('sales_order_item')],
                'si.item_id = state.sales_item_id',
                []
            )
            ->join(
                ['so' => $this->getTable('sales_order')],
                'si.order_id = so.entity_id',
                []
            )
            ->where('so.customer_id = ?', $customerId);

        if (isset($interval[self::MIN])) {
            $sign = ($direction == self::RIGHT) ? '>=' : '>';
            $select->where('state.sort_order '. $sign .' ?', $interval[self::MIN]);
        }

        if (isset($interval[self::MAX])) {
            $sign = ($direction == self::LEFT) ? '<=' : '<';
            $select->where('state.sort_order '. $sign .' ?', $interval[self::MAX]);
        }

        return $select;
    }

    /**
     * @param $direction
     * @return string
     */
    protected function getShiftExpression($direction)
    {
        if ($direction == self::RIGHT) {
            return 'state.sort_order+1';
        } elseif ($direction == self::LEFT) {
            return 'state.sort_order-1';
        } else {
            throw new LocalizedException(__('Incorrect shift direction'));
        }
    }

    /**
     * Update status by ids
     *
     * @param [] $id
     * @param int $status
     * @return int
     */
    public function updateStatus($ids, $status)
    {
        $connection = $this->getConnection();
        return $connection->update(
            $this->_getMainInfoTable(),
            [
                'status' => $status,
            ],
            $connection->quoteInto('entity_id IN (?)', $ids)
        );
    }

    /**
     * @param OrderItemCollection $collection
     * @param $customerId
     */
    public function joinExtraData(OrderItemCollection $collection, $customerId = null)
    {
        $collection->getSelect()->join(
            ['state' => $this->getTable('digidirect_sales_order_item_state')],
            'state.sales_item_id = main_table.item_id',
            ['state_status' => 'state.status']
        );
        $collection->getSelect()->join(
            ['order' => $this->getTable('sales_order')],
            'order.entity_id = main_table.order_id',
            []
        );
        $collection->getSelect()->joinLeft(
            ['link' => $this->getTable('catalog_category_product')],
            'link.product_id = main_table.product_id',
            []
        );
        $collection->getSelect()->where('ISNULL(main_table.parent_item_id)');
        if ($customerId) {
            $collection->getSelect()->where('order.customer_id = ?', $customerId);
        }
        $collection->getSelect()->group('main_table.item_id');
        $collection->getSelect()->order('state.sort_order', OrderItemCollection::SORT_ORDER_ASC);
    }

    /**
     * @param OrderItemCollection $collection
     * @param $categoryId
     */
    public function filterCategories(OrderItemCollection $collection, $categoryId)
    {
        $collection->getSelect()->where('link.category_id = ?',  $categoryId);
    }
}
