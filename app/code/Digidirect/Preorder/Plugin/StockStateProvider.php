<?php

namespace Digidirect\PreOrder\Plugin;

use Digidirect\PreOrder\Helper\Data as PreorderHelper;

/**
 * Class StockStateProvider
 *
 * @package Digidirect\PreOrder\Plugin
 */
class StockStateProvider
{
    /**
     * @var \Digidirect\PreOrder\Helper\Data
     */
    private $preOrderHelper;

    /**
     * StockStateProvider constructor.
     *
     * @param \Digidirect\PreOrder\Helper\Data $preOrderHelper
     */
    public function __construct(
        PreorderHelper $preOrderHelper
    ) {
        $this->preOrderHelper = $preOrderHelper;
    }

    /**
     * Add preorder functionality when check qty
     *
     * @param \Magento\CatalogInventory\Model\StockStateProvider $subject
     * @param \Closure $closure
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @param int $qty
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCheckQty(
        \Magento\CatalogInventory\Model\StockStateProvider $subject,
        \Closure $closure,
        \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem,
        $qty
    ) {
        $result = $closure($stockItem, $qty);
        if ($result) {
            return $result;
        }

        return $this->preOrderHelper->checkStockItemQty($stockItem)
            && $this->preOrderHelper->checkQtyAvailability($stockItem, $qty);
    }

    /**
     * Add preorder functionality when verify stock
     *
     * @param \Magento\CatalogInventory\Model\StockStateProvider $subject
     * @param \Closure $closure
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundVerifyStock(
        \Magento\CatalogInventory\Model\StockStateProvider $subject,
        \Closure $closure,
        \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
    ) {
        $result = $closure($stockItem);
        if (!$result) {
            return $result;
        }

        return $this->preOrderHelper->verifyStockItem($stockItem);
    }
}
