<?php
namespace Ewave\ProductPriority\Model\Priority\Sort;

/**
 * Class RevenueSort
 * @package Ewave\ProductPriority\Model\Priority\Sort
 */
class RevenueSort extends \Ewave\ProductPriority\Model\Priority\CalculateAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function _getPrioritySortItems(array $orderIds)
    {
        $orderItemsSelect = $this->_connection->select()
            ->from(
                $this->_resource->getTableName('sales_order_item'),
                [
                    'item_id',
                    'product_id',
                    'qty_ordered',
                    'price',
                    'parent_item_id'
                ]
            )
            ->where('order_id IN(?)', $orderIds);
        $data = $this->_connection->fetchAssoc($orderItemsSelect);

        $data = $this->_prepareItemProducts($data);
        $data = $this->_sortByRevenue($data);

        return $data;
    }

    /**
     * Set margin value for parent product
     * @param array $itemArray
     * @return array
     */
    protected function _prepareItemProducts(array $itemArray)
    {
        $sortedItems = [];
        foreach ($itemArray as $itemId => $item) {
            if (isset($sortedItems[$itemId])) {
                continue;
            }
            if (null === $item['parent_item_id']) {
                $sortedItems[$itemId] = $item;
                continue;
            }
            if (isset($itemArray[$item['parent_item_id']])) {
                $childProduct = $item;
                $parentProduct = $itemArray[$item['parent_item_id']];
                $childProduct['price'] = $parentProduct['price'];
                $sortedItems[$itemId] = $childProduct;
                $sortedItems[$childProduct['parent_item_id']] = $parentProduct;
            }
        }
        return $sortedItems;
    }

    /**
     * Revenue calculate
     * @param array $orderItemsData
     * @return array
     */
    protected function _sortByRevenue(array $orderItemsData)
    {
        $productSort = [];

        foreach ($orderItemsData as $orderItem) {
            if (!isset($productSort[$orderItem['product_id']])) {
                $productSort[$orderItem['product_id']] = $orderItem['qty_ordered'] * $orderItem['price'];
            } else {
                $productSort[$orderItem['product_id']] += $orderItem['qty_ordered'] * $orderItem['price'];
            }
        }

        arsort($productSort);

        return $productSort;
    }
}
