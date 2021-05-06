<?php

namespace Digidirect\PreOrder\Model\Preorder\Product;

/**
 * Class Configurable
 *
 * @package Digidirect\PreOrder\Model\Preorder\Product
 */
class Configurable extends CompositeAbstract
{
    /**
     * {@inheritdoc}
     */
    public function checkProductPreorder(\Magento\Catalog\Api\Data\ProductInterface $product, $requiredQty = null)
    {
        /** @var \Magento\Catalog\Model\Product $product */

        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $typeInstance */
        $typeInstance = $product->getTypeInstance();
        $elementaryProducts = $typeInstance->getUsedProducts($product);
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
     * {@inheritdoc}
     */
    public function isQuoteItemPreorder(\Magento\Quote\Api\Data\CartItemInterface $quoteItem)
    {
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */

        /** @var \Magento\Quote\Model\Quote\Item\Option $option */
        $option = $quoteItem->getOptionByCode('simple_product');
        $simpleProduct = $option->getProduct();
        if (!$simpleProduct instanceof \Magento\Catalog\Model\Product) {
            return false;
        }
        return $this->simpleProductPreorder->isProductPreorder($simpleProduct, (int)$quoteItem->getQty());
    }
}
