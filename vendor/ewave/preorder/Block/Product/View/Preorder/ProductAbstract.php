<?php

namespace Ewave\PreOrder\Block\Product\View\Preorder;

/**
 * Class ProductAbstract
 *
 * @package Ewave\PreOrder\Block\Product\View\Preorder
 */
class ProductAbstract extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * @var \Ewave\PreOrder\Helper\Data
     */
    protected $preOrderHelper;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * ProductAbstract constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Ewave\PreOrder\Helper\Data $preOrderHelper
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Ewave\PreOrder\Helper\Data $preOrderHelper,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        array $data = []
    ) {
        $this->preOrderHelper = $preOrderHelper;
        $this->stockItemRepository = $stockItemRepository;

        parent::__construct($context, $arrayUtils, $data);
    }

    /**
     * Can show block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        return $this->preOrderHelper->getConfig()->preordersEnabled();
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
     * Get preorder note
     *
     * @return string
     */
    public function getPreorderNote()
    {
        return $this->preOrderHelper->getProductPreorderNote($this->getProduct());
    }

    /**
     * @return bool
     */
    public function isAutoEnabled()
    {
        $stockItem = $this->stockItemRepository->get($this->getProduct()->getId());
        return $this->preOrderHelper->checkStockItemQty($stockItem);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if ($this->canShowBlock()) {
            return parent::_toHtml();
        }
        return '';
    }
}
