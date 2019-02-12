<?php
namespace Ewave\AdvancedInventory\Model\ResourceModel\AdvancedInventory;

use Ewave\AbstractEntity\Model\ResourceModel\AbstractEntity\Collection as AbstractEntityCollection;
use Ewave\AdvancedInventory\Model\ResourceModel\AdvancedInventoryStock;

class Collection extends AbstractEntityCollection
{
    /**
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinAdvancedInventoryStock();
        parent::_renderFiltersBefore();
    }

    /**
     * @return $this
     */
    public function joinAdvancedInventoryStock()
    {
        $this->getSelect()->joinInner(
            ['stock' => $this->getTable(AdvancedInventoryStock::TABLE_NAME)],
            new \Zend_Db_Expr('e.entity_id = stock.abstract_entity_id'),
            ['stock_id']
        );
        return $this;
    }

    /**
     * @param int $productId
     * @return $this
     */
    public function joinStockItem($productId = null)
    {
        $productCondition = '';
        if ($productId !== null) {
            $productCondition = $this->getConnection()->quoteInto(' AND stock_item.product_id = ?', $productId);
        }

        $this->getSelect()->joinLeft(
            ['stock_item' => $this->getTable('cataloginventory_stock_item')],
            new \Zend_Db_Expr('stock.stock_id = stock_item.stock_id' . $productCondition),
            [
                'item_id',
                'qty',
                'manage_stock',
                'is_in_stock',
                new \Zend_Db_Expr('IF(stock_item.item_id,stock_item.is_in_stock,0) as in_stock')
            ]
        );

        return $this;
    }
}
