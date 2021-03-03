<?php

namespace Digidirect\PreOrder\Block\Product\View\Preorder;

/**
 * Class ProductConfigurable
 *
 * @package Digidirect\PreOrder\Block\Product\View\Preorder
 */
class ProductConfigurable extends ProductAbstract
{
    /**
     * Get configurable attributes
     *
     * @return int[]
     */
    public function getConfigurableAttributes()
    {
        $attributes = [];
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $typeInstance */
        $typeInstance = $this->getProduct()->getTypeInstance();
        $allowedAttributes = $typeInstance->getConfigurableAttributes($this->getProduct());
        foreach ($allowedAttributes as $attribute) {
            $attributes[$attribute->getProductAttribute()->getId()] = 0;
        }

        return $attributes;
    }

    /**
     * Get product PreOrder Map
     *
     * @return string[]
     */
    public function getProductPreorderMap()
    {
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $typeInstance */
        $typeInstance = $this->getProduct()->getTypeInstance();
        $elementaryProducts = $typeInstance->getUsedProducts($this->getProduct());
        $allowedAttributes = $typeInstance->getConfigurableAttributes($this->getProduct());

        $map = [];
        foreach ($elementaryProducts as $product) {
            /** @var \Magento\Catalog\Model\Product $product */
            if ($this->preOrderHelper->isProductPreorder($product)) {
                $map[$product->getId()] = [
                    'cartLabel'  => $this->preOrderHelper->getProductPreorderCartLabel($product),
                    'note'       => $this->preOrderHelper->getProductPreorderNote($product),
                    'attributes' => []
                ];

                foreach ($allowedAttributes as $attribute) {
                    $productAttribute = $attribute->getProductAttribute();
                    $productAttributeId = $productAttribute->getId();
                    $attributeValue = $product->getData($productAttribute->getAttributeCode());
                    $map[$product->getId()]['attributes'][$productAttributeId] = $attributeValue;
                }
            }
        }

        return $map;
    }

    /**
     * Is all products PreOrder
     *
     * @return bool
     */
    public function isAllProductsPreorder()
    {
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $typeInstance */
        $typeInstance = $this->getProduct()->getTypeInstance();
        $elementaryProducts = $typeInstance->getUsedProducts($this->getProduct());
        $isAllProductsPreorder = true;
        foreach ($elementaryProducts as $product) {
            if (!$this->preOrderHelper->isProductPreorder($product)) {
                $isAllProductsPreorder = false;
            }
        }

        return $isAllProductsPreorder;
    }
}
