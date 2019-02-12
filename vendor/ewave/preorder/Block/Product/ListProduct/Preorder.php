<?php

namespace Ewave\PreOrder\Block\Product\ListProduct;

use Magento\Framework\View\Element\Template;

/**
 * Class Preorder
 *
 * @package Ewave\PreOrder\Block\Product\ListProduct
 */
class Preorder extends Template
{
    const PRODUCT = 'product';

    /**
     * @var \Ewave\PreOrder\Helper\Data
     */
    protected $preOrderHelper;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * Preorder constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ewave\PreOrder\Helper\Data $preOrderHelper
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Ewave\PreOrder\Helper\Data $preOrderHelper,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        array $data = []
    ) {
        $this->preOrderHelper = $preOrderHelper;
        $this->stockItemRepository = $stockItemRepository;

        parent::__construct($context, $data);
    }

    /**
     * Get preorder note
     *
     * @return string
     */
    public function getPreorderNote()
    {
        return $this->preOrderHelper->getProductPreorderNote($this->getProduct());
    }

    /**
     * Get cart label
     *
     * @return string
     */
    public function getCartLabel()
    {
        return $this->preOrderHelper->getProductPreorderCartLabel($this->getProduct());
    }

    /**
     * Set product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function setProduct(\Magento\Catalog\Model\Product $product)
    {
        $this->setData(static::PRODUCT, $product);
        return $this;
    }

    /**
     * Get product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->getData(static::PRODUCT);
    }

    /**
     * @return bool
     */
    public function isAutoEnabled()
    {
        $stockItem = $this->stockItemRepository->get($this->getProduct()->getId());
        return $this->preOrderHelper->checkStockItemQty($stockItem);
    }
}
