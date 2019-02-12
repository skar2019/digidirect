<?php

namespace Ewave\PreOrder\Plugin;

use Ewave\PreOrder\Helper\Data as PreorderHelper;

/**
 * Class StockStateProvider
 *
 * @package Ewave\PreOrder\Plugin
 */
class StockStateProvider
{
    /**
     * @var \Ewave\PreOrder\Helper\Data
     */
    private $preOrderHelper;

    /**
     * StockStateProvider constructor.
     *
     * @param \Ewave\PreOrder\Helper\Data $preOrderHelper
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
    public function aroundVerifyStock
    (
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
