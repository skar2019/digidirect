<?php

namespace Ewave\PreOrder\Model\Preorder\Product;

/**
 * Class Grouped
 *
 * @package Ewave\PreOrder\Model\Preorder\Product
 */
class Grouped extends CompositeAbstract
{
    /**
     * {@inheritdoc}
     */
    public function checkProductPreorder(\Magento\Catalog\Api\Data\ProductInterface $product, $requiredQty = null)
    {
        /** @var \Magento\Catalog\Model\Product $product */

        /** @var \Magento\GroupedProduct\Model\Product\Type\Grouped $typeInstance */
        $typeInstance = $product->getTypeInstance();
        $elementaryProducts = $typeInstance->getAssociatedProducts($product);
        if (empty($elementaryProducts)) {
            return false;
        }

        $result = true;
        foreach ($elementaryProducts as $elementary) {
            /** @var \Magento\Catalog\Model\Product $elementary */
            if (!$this->simpleProductPreorder->isProductPreorder($elementary, $requiredQty)) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * Whether quote item is Pre-Order
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $quoteItem
     * @return bool
     */
    public function isQuoteItemPreorder(\Magento\Quote\Api\Data\CartItemInterface $quoteItem)
    {
        return false;
    }
}
