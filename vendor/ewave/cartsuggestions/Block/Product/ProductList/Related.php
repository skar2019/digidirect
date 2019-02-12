<?php
namespace Ewave\CartSuggestions\Block\Product\ProductList;

/**
 * Class Related
 * @package Ewave\CartSuggestions\Block\ProductList
 */
class Related extends \Magento\Catalog\Block\Product\ProductList\Related
{
    /**
     * @var string
     */
    protected $_template = 'Magento_Catalog::product/list/items.phtml';

    /**
     * @return $this
     */
    protected function _prepareData()
    {
        if ($this->_itemCollection === null) {
            $this->loadProductCollection();
        }

        return $this;
    }

    /**
     * Set product to block
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function setProduct(\Magento\Catalog\Model\Product $product)
    {
        $this->_itemCollection = null;
        $this->addData(['product' => $product]);
        return $this;
    }

    /**
     * Check has product related or not
     *
     * @return bool
     */
    public function hasProducts()
    {
        return $this->getItems()->count() > 0;
    }

    /**
     * Retrieve product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|null
     */
    public function getItems()
    {
        if ($this->_itemCollection === null) {
            $this->loadProductCollection();
        }
        return $this->_itemCollection;
    }

    /**
     * Load product collection
     *
     * @return $this
     */
    public function loadProductCollection()
    {
        $product = $this->getProduct();
        /* @var $product \Magento\Catalog\Model\Product */

        $this->_itemCollection = $product->getRelatedProductCollection()->addAttributeToSelect(
            'required_options'
        )->setPositionOrder()->addStoreFilter();

        if ($this->moduleManager->isEnabled('Magento_Checkout')) {
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        $this->_itemCollection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

        $this->_itemCollection->setPageSize((int) $this->getProductNumber());

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }
}
