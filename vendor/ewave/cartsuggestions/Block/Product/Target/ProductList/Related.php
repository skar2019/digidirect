<?php
namespace Ewave\CartSuggestions\Block\Product\Target\ProductList;

/**
 * Class Related
 * @package Ewave\CartSuggestions\Block\Product\Target\ProductList
 */
class Related extends \Magento\TargetRule\Block\Catalog\Product\ProductList\Related
{
    /**
     * Set product to block
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function setProduct(\Magento\Catalog\Model\Product $product)
    {
        $this->_items = null;
        $this->_linkCollection = null;
        $this->addData(['product' => $product]);
        return $this;
    }

    /**
     * Retrieve product
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->getData('product');
    }

    /**
     * Check has product related or not
     *
     * @return bool
     */
    public function hasProducts()
    {
        return $this->hasItems();
    }

    /**
     * Slice items to limit
     *
     * @return $this
     */
    protected function _sliceItems()
    {
        if ($this->_items !== null) {
            if ($this->isShuffled()) {
                $this->_items = array_slice($this->_items, 0, $this->getProductNumber(), true);
            } else {
                $this->_items = array_slice($this->_items, 0, $this->getPositionLimit(), true);
            }
        }
        return $this;
    }
}
