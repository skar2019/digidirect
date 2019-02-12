<?php
namespace Ewave\AdvancedInventory\Api;

interface AdvancedInventoryRepositoryInterface
{
    /**
     * @param int $productId
     * @param int $stockId
     * @param int $scopeId
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    public function getStockItem($productId, $stockId = null, $scopeId = null);

    /**
     * @param int $stockId
     * @return \Ewave\AbstractEntity\Api\Data\AbstractEntityInterface
     */
    public function getAbstractEntity($stockId = null);

    /**
     * @param int $productId
     * @param bool $addDefaultStock
     * @return float
     */
    public function getTotalStockQuantity($productId, $addDefaultStock = false);
}
