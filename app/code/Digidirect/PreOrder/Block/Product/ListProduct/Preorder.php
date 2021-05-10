<?php

namespace Digidirect\PreOrder\Block\Product\ListProduct;

use Magento\Framework\View\Element\Template;
use Magento\CatalogInventory\Model\StockRegistry;
use Magento\Framework\App\ObjectManager;

/**
 * Class Preorder
 *
 * @package Digidirect\PreOrder\Block\Product\ListProduct
 */
class Preorder extends Template
{
    const PRODUCT = 'product';

    /**
     * @var \Digidirect\PreOrder\Helper\Data
     */
    protected $preOrderHelper;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * @var StockRegistry
     */
    protected $stockRegistry;

    /**
     * Preorder constructor.
     * @param Template\Context $context
     * @param \Digidirect\PreOrder\Helper\Data $preOrderHelper
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     * @param StockRegistry|null $stockRegistry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Digidirect\PreOrder\Helper\Data $preOrderHelper,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        StockRegistry $stockRegistry = null,
        array $data = []
    ) {
        $this->preOrderHelper = $preOrderHelper;
        $this->stockItemRepository = $stockItemRepository;
        $this->stockRegistry = $stockRegistry ?: ObjectManager::getInstance()->get(StockRegistry::class);

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
        $stockItem = $this->stockRegistry->getStockItem(
            $this->getProduct()->getId(),
            $this->getProduct()->getStore()->getWebsiteId()
        );
        return $this->preOrderHelper->checkStockItemQty($stockItem);
    }
}
