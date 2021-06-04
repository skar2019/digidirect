<?php

namespace Ewave\PreOrder\Model\Preorder\Product;

use Ewave\PreOrder\Helper\Data as PreOrderHelper;

/**
 * Class CompositeAbstract
 *
 * @package Ewave\PreOrder\Model\Preorder\Product
 */
abstract class CompositeAbstract extends SimpleAbstract
{
    /**
     * @var \Ewave\PreOrder\Model\Preorder\Product\Simple
     */
    protected $simpleProductPreorder;

    /**
     * Simple constructor.
     *
     * @param \Ewave\PreOrder\Helper\Data $preOrderHelper
     * @param \Ewave\PreOrder\Model\Preorder\Product\Simple $simpleProductPreorder
     */
    public function __construct(
        PreOrderHelper $preOrderHelper,
        Simple $simpleProductPreorder
    ) {
        $this->simpleProductPreorder = $simpleProductPreorder;

        parent::__construct($preOrderHelper);
    }

    /**
     * Check product preorder status
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int $requiredQty
     * @return mixed
     */
    abstract public function checkProductPreorder(
        \Magento\Catalog\Api\Data\ProductInterface $product,
        $requiredQty = null
    );

    /**
     * {@inheritdoc}
     */
    public function isProductPreorder(\Magento\Catalog\Api\Data\ProductInterface $product, $requiredQty = null)
    {
        if (!$this->preOrderHelper->getConfig()->isDiscoverCompositeOptionsEnabled()) {
            return false;
        }

        return $this->checkProductPreorder($product, $requiredQty);
    }
}
