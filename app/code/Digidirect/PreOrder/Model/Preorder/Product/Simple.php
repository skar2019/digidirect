<?php

namespace Digidirect\PreOrder\Model\Preorder\Product;

use Digidirect\PreOrder\Helper\Data as PreOrderHelper;

/**
 * Class Simple
 *
 * @package Digidirect\PreOrder\Model\Preorder\Product
 */
class Simple extends SimpleAbstract
{
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * Simple constructor.
     *
     * @param \Digidirect\PreOrder\Helper\Data $preOrderHelper
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     */
    public function __construct(
        PreOrderHelper $preOrderHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    ) {
        $this->stockRegistry = $stockRegistry;

        parent::__construct($preOrderHelper);
    }

    /**
     * {@inheritdoc}
     */
    public function isProductPreorder(\Magento\Catalog\Api\Data\ProductInterface $product, $requiredQty = null)
    {
        /** @var \Magento\CatalogInventory\Model\Stock\Item $inventory */
        $inventory = $this->stockRegistry->getStockItem($product->getId());
        $isPreorderOptionSelected = $inventory->getBackorders() == PreOrderHelper::BACKORDERS_PREORDER_OPTION;
        $disabledByQty = $this->preOrderHelper->getConfig()->disableForPositiveQty()
                         && $inventory->getQty() > ($requiredQty ?? 1);

        $result = $isPreorderOptionSelected && !$disabledByQty;
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isQuoteItemPreorder(\Magento\Quote\Api\Data\CartItemInterface $quoteItem)
    {
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        return $this->isProductPreorder($quoteItem->getProduct(), (int)$quoteItem->getQty());
    }
}
