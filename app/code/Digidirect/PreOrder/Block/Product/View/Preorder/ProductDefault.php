<?php

namespace Digidirect\PreOrder\Block\Product\View\Preorder;

/**
 * Class ProductDefault
 *
 * @package Digidirect\PreOrder\Block\Product\View\Preorder
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
