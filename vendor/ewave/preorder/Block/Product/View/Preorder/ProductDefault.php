<?php

namespace Ewave\PreOrder\Block\Product\View\Preorder;

/**
 * Class ProductDefault
 *
 * @package Ewave\PreOrder\Block\Product\View\Preorder
 */
class ProductDefault extends ProductAbstract
{
    /**
     * {@inheritdoc}
     */
    public function canShowBlock()
    {
        return parent::canShowBlock() && $this->preOrderHelper->isProductPreorder($this->getProduct());
    }
}
