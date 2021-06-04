<?php
namespace Ewave\PreOrder\Plugin\Magento\CatalogInventory\Model\Stock;

/**
 * Class Item
 * @package Ewave\PreOrder\Plugin\Magento\CatalogInventory\Model\Stock
 */
class Item
{
    /**
     * @var \Ewave\PreOrder\Helper\Data
     */
    protected $preOrderHelper;

    /**
     * AbstractType constructor.
     * @param \Ewave\PreOrder\Helper\Data $preOrderHelper
     */
    public function __construct(
        \Ewave\PreOrder\Helper\Data $preOrderHelper
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
