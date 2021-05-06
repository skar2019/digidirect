<?php
namespace Digidirect\PreOrder\Plugin\Magento\CatalogInventory\Model\Stock;

/**
 * Class Item
 * @package Digidirect\PreOrder\Plugin\Magento\CatalogInventory\Model\Stock
 */
class Item
{
    /**
     * @var \Digidirect\PreOrder\Helper\Data
     */
    protected $preOrderHelper;

    /**
     * AbstractType constructor.
     * @param \Digidirect\PreOrder\Helper\Data $preOrderHelper
     */
    public function __construct(
        \Digidirect\PreOrder\Helper\Data $preOrderHelper
    ) {
        $this->preOrderHelper = $preOrderHelper;
    }

    /**
     * @param \Magento\CatalogInventory\Model\Stock\Item $subject
     * @param callable $process
     * @return bool
     */
    public function aroundGetIsInStock(
        \Magento\CatalogInventory\Model\Stock\Item $subject,
        callable $process
    ) {
        $result = $process();
        if (!$result) {
            $result = $this->preOrderHelper->checkStockItemQty($subject);
        }
        return $result;
    }
}
