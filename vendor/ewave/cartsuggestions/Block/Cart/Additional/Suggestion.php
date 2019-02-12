<?php
namespace Ewave\CartSuggestions\Block\Cart\Additional;

use Magento\Framework\View\Element\Template;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Ewave\CartSuggestions\Helper\Data as Helper;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Class Related
 * @package Ewave\CartSuggestions\Block\ProductList
 */
class Suggestion extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Suggestion constructor.
     * @param Template\Context $context
     * @param StockRegistryInterface $stockRegistry
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        StockRegistryInterface $stockRegistry,
        Helper $helper,
        array $data = []
    ) {
        $this->stockRegistry = $stockRegistry;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->helper->isModuleEnabled()) {
            $this->_product = $this->getItem()->getProduct();
            $this->_prepareProduct();
            return parent::_toHtml();
        }
    }

    /**
     * Retrieve product stock item
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    public function getProductStock(\Magento\Catalog\Model\Product $product)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        return $stockItem;
    }

    /**
     * Check is product in stock
     *
     * @return bool
     */
    public function isProductInStock()
    {
        $stockItem = $this->getProductStock($this->_product);
        return (bool) $stockItem->getIsInStock();
    }

    /**
     * Prepare product for block
     *
     * @return $this
     */
    protected function _prepareProduct()
    {
        $isInStock = $this->isProductInStock();

        if ($isInStock && $this->_product->getTypeInstance() instanceof Configurable) {
            $this->_product = $this->getItem()->getOptionByCode('simple_product')->getProduct();
        }

        return $this;
    }

    /**
     * Retrieve custom related block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     */
    public function getRelatedProductBlock()
    {
        if ($this->getChildBlock($this->getTargetRelatedBlockName())->setProduct($this->_product)->hasItems()) {
            $block = $this->getChildBlock($this->getTargetRelatedBlockName());
        } else {
            $block = $this->getChildBlock($this->getRelatedBlockName())->setProduct($this->_product);
        }

        return $block;
    }
}
