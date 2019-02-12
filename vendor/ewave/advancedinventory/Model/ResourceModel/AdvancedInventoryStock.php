<?php
namespace Ewave\AdvancedInventory\Model\ResourceModel;

use Ewave\AdvancedInventory\Api\Data\AdvancedInventoryStockInterface;
use Magento\CatalogInventory\Model\Stock;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AdvancedInventoryStock extends AbstractDb
{
    const TABLE_NAME = 'ewave_advancedinventory_stock';

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, AdvancedInventoryStockInterface::STOCK_ID);
    }

    /**
     * @param int $entityId
     * @return int
     */
    public function getStockIdByEntityId($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable(), $this->getIdFieldName())
            ->where(AdvancedInventoryStockInterface::ABSTRACT_ENTITY_ID . ' = :entityId')
            ->limit(1);

        return (int)$connection->fetchOne($select, ['entityId' => $entityId]);
    }

    /**
     * @param int $stockId
     * @return int
     */
    public function getEntityIdByStockId($stockId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getMainTable(),
            AdvancedInventoryStockInterface::ABSTRACT_ENTITY_ID
        )
            ->where(AdvancedInventoryStockInterface::STOCK_ID . ' = :stockId')
            ->limit(1);

        return (int)$connection->fetchOne($select, ['stockId' => $stockId]);
    }

    /**
     * @param int $productId
     * @param bool $addDefaultStock
     * @return float
     */
    public function getTotalStockQuantity($productId, $addDefaultStock = false)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('cataloginventory_stock_item'),
            'SUM(qty) as total_qty'
        )
            ->where('product_id = :productId')
            ->limit(1);

        if (!$addDefaultStock) {
            $select->where('stock_id != ?', Stock::DEFAULT_STOCK_ID);
        }

        return (float)$connection->fetchOne($select, ['productId' => $productId]);
    }
}
