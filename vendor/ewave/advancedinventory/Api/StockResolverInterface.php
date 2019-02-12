<?php
namespace Ewave\AdvancedInventory\Api;

interface StockResolverInterface
{
    /**
     * @param int $scopeId
     * @return int
     */
    public function getCurrentStockId($scopeId = null);

    /**
     * @return array
     */
    public function getAllowedProductTypes();
}
