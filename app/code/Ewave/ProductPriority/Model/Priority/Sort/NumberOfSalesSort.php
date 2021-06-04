<?php
namespace Ewave\ProductPriority\Model\Priority\Sort;

use Magento\Framework\Data\Collection;

/**
 * Class NumberOfSalesSort
 * @package Ewave\ProductPriority\Model\Priority\Sort
 */
class NumberOfSalesSort extends \Ewave\ProductPriority\Model\Priority\CalculateAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function _getPrioritySortItems(array $orderIds)
    {
        $sortBy = $this->_priorityConfigHelper->getSortBy();

        $orderItemsSelect = $this->_connection->select()
            ->from(
                $this->_resource->getTableName('sales_order_item'),
                [
                    'product_id',
                    $sortBy => new \Zend_Db_Expr('SUM(qty_ordered)')
                ]
            )
            ->where('order_id IN(?)', $orderIds)
            ->group('product_id')
            ->order(sprintf('%s %s', $sortBy, Collection::SORT_ORDER_DESC));
        $data = $this->_connection->fetchPairs($orderItemsSelect);

        return $data;
    }
}
