<?php
namespace Ewave\AdvancedInventory\Model\ResourceModel\StockItem;

use Ewave\AbstractEntity\Model\ResourceModel\AbstractEntity\Collection as AbstractEntityCollection;
use Ewave\AdvancedInventory\Model\ResourceModel\AdvancedInventoryStock;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init(
            \Magento\CatalogInventory\Model\Stock\Item::class,
            \Magento\CatalogInventory\Model\ResourceModel\Stock\Item::class
        );
    }

    /**
     * @param int $entityId
     * @return $this
     */
    public function joinAbstractEntity($entityId = null)
    {
        $this->getSelect()->join(
            ['ai' => $this->getTable('ewave_advancedinventory_stock')],
            new \Zend_Db_Expr('main_table.stock_id = ai.stock_id'),
            []
        );

        $this->getSelect()->join(
            ['product' => $this->getTable('catalog_product_entity')],
            new \Zend_Db_Expr('main_table.product_id = product.entity_id'),
            ['sku']
        );

        if ($entityId !== null) {
            $this->getSelect()->where('ai.abstract_entity_id = ?', $entityId);
        }

        return $this;
    }
}
