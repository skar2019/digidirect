<?php

namespace Ewave\PreOrder\Api;

/**
 * Interface PreorderInterface
 *
 * @package Ewave\PreOrder\Api
 */
interface PreorderInterface
{
    /**
     * Whether product is Pre-Order
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int|null $requiredQty
     * @return bool
     */
    public function isProductPreorder(\Magento\Catalog\Api\Data\ProductInterface $product, $requiredQty = null);

    /**
     * Whether quote item is Pre-Order
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $quoteItem
     * @return bool
     */
    public function isQuoteItemPreorder(\Magento\Quote\Api\Data\CartItemInterface $quoteItem);
}
