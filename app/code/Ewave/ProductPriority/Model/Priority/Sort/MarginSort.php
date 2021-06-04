<?php
namespace Ewave\ProductPriority\Model\Priority\Sort;

use Magento\Catalog\Model\Product;

/**
 * Class RevenueSort
 * @package Ewave\ProductPriority\Model
 */
class MarginSort extends \Ewave\ProductPriority\Model\Priority\CalculateAbstract
{
    /**
     * @param array $orderIds
     * @return array
     */
    protected function _getPrioritySortItems(array $orderIds)
    {
        $attrCost = $this->_eavConfig->getAttribute(Product::ENTITY, 'cost');
        $attrPrice = $this->_eavConfig->getAttribute(Product::ENTITY, 'price');

        $orderItemsSelect = $this->_connection->select()
            ->from(
                ['i_t' => $this->_resource->getTableName('sales_order_item')],
                [
                    'item_id',
                    'qty_ordered',
                    'product_id',
                    'product_item_price' => 'price',
                    'order_id',
                    'parent_item_id'
                ]
            )->joinLeft(
                ['p_t' => $attrPrice->getBackendTable()],
                'i_t.product_id = p_t.row_id AND p_t.attribute_id=' . (int)$attrPrice->getId(),
                ['product_price' => 'value']
            )->joinLeft(
                ['c_t' => $attrCost->getBackendTable()],
                'i_t.product_id = c_t.row_id AND c_t.attribute_id=' . (int)$attrCost->getId(),
                ['cost' => 'value']
            )
            ->where('order_id IN(?)', $orderIds);
        $data = $this->_connection->fetchAssoc($orderItemsSelect);

        $data = $this->_prepareItemProducts($data);
        $marginData = $this->_sortByMargin($data);

        return $marginData;
    }

    /**
     * Prepare values for parent and child products
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
            if ($item['parent_item_id'] === null) {
                $sortedItems[$itemId] = $item;
                continue;
            }
            if (isset($itemArray[$item['parent_item_id']])) {
                $childProduct = $item;
                $parentProduct = $itemArray[$item['parent_item_id']];
                $childProduct['product_item_price'] = $parentProduct['product_item_price'];
                $parentProduct['cost'] = $childProduct['cost'];
                $sortedItems[$itemId] = $childProduct;
                $sortedItems[$childProduct['parent_item_id']] = $parentProduct;
            }
        }
        return $sortedItems;
    }

    /**
     * Margin calculate
     * @param array $orderItemsData
     * @return array
     */
    protected function _sortByMargin(array $orderItemsData)
    {
        $productMargin = [];

        foreach ($orderItemsData as $orderItem) {
            if (!isset($productMargin[$orderItem['product_id']])) {
                $productMargin[$orderItem['product_id']] = $this->_calculateMarginForOrderItem($orderItem);
            } else {
                $productMargin[$orderItem['product_id']] += $this->_calculateMarginForOrderItem($orderItem);
            }
        }

        arsort($productMargin);

        return $productMargin;
    }

    /**
     * Calculate by formula
     * @param array $orderItemData
     * @return mixed
     */
    protected function _calculateMarginForOrderItem(array $orderItemData)
    {
        $cost = $orderItemData['cost'] ? $orderItemData['cost'] :
            $orderItemData['product_price'];
        $eachProductPriceInItem = $orderItemData['product_item_price'];
        return $orderItemData['qty_ordered'] * ($eachProductPriceInItem - $cost);
    }
}
