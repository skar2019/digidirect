<?php

namespace Ewave\ExtendedCart\Block\Checkout\AddToCartConfirmationPopup;

class PromotionProducts extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Ewave\ExtendedCart\Helper\AddToCartConfirmationPopup
     */
    protected $addToCartConfirmationPopupHelper;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $promotionProducts;

    /**
     * @var array
     */
    protected $blocksMap = [];

    /**
     * @var array
     */
    protected $listingTypesMap = [];

    /**
     * @var object
     */
    protected $productsBlock;

    /**
     * PromotionProducts constructor.
     * @param \Ewave\ExtendedCart\Helper\AddToCartConfirmationPopup $addToCartConfirmationPopupHelper
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param array $data
     */
    public function __construct(
        \Ewave\ExtendedCart\Helper\AddToCartConfirmationPopup $addToCartConfirmationPopupHelper,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Block\Product\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->addToCartConfirmationPopupHelper = $addToCartConfirmationPopupHelper;
        $this->productMetadata = $productMetadata;
        $this->objectManager = $objectManager;
    }

    /**
     * @return array
     */
    protected function getBlockTypes()
    {
        return $this->getData('block_types') ?? [];
    }

    /**
     * @return array
     */
    protected function getListingTypes()
    {
        return $this->getData('listing_types') ?? [];
    }

    /**
     * @return string
     */
    public function getType()
    {
        $blockType = $this->addToCartConfirmationPopupHelper->getPromotionBlock();
        $listingTypes = $this->getListingTypes();
        return $listingTypes[$blockType] ?? null;
    }

    /**
     * @return false|object
     */
    protected function getProductsBlock()
    {
        if ($this->productsBlock === null) {
            $this->productsBlock = false;
            $blockType = $this->addToCartConfirmationPopupHelper->getPromotionBlock();
            $blockTypes = $this->getBlockTypes();
            $blockClass = $blockTypes[$blockType] ?? null;
            if ($blockClass) {
                $this->productsBlock = $this->objectManager->get($blockClass);
            }
        }
        return $this->productsBlock;
    }

    /**
     * @return array
     */
    protected function _getPromotionProducts()
    {
        $productsCount = $this->getItemLimit();
        if ($productsCount <= 0) {
            return [];
        }

        $block = $this->getProductsBlock();
        if (!$block) {
            return [];
        }

        if (method_exists($block, 'getItems')) {
            $products = $block->getItems();
        } elseif (method_exists($block, 'getItemCollection')) {
            $products = $block->getItemCollection();
        } else {
            return [];
        }

        if (!count($products)) {
            return [];
        }

        if (is_object($products)) {
            $i = 0;
            foreach ($products as $item) {
                $i++;
                if ($i > $productsCount) {
                    $products->removeItemByKey($item->getId());
                }
            }
        } else {
            $products = array_slice($products, 0, $productsCount);
        }

        if (method_exists($block, 'getItems') && method_exists($block, 'getItemCollection')) {
            $productIds = [];
            foreach ($products as $product) {
                $productIds[] = $product->getId();
            }
            $collection = $block->getItemCollection();
            foreach ($collection as $product) {
                if (!in_array($product->getId(), $productIds)) {
                    $products->removeItemByKey($product->getId());
                }
            }
        }

        return $products;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        if ($this->promotionProducts === null) {
            $this->promotionProducts = $this->_getPromotionProducts();
        }
        return $this->promotionProducts;
    }

    /**
     * @return array
     */
    public function getItemCollection()
    {
        $block = $this->getProductsBlock();
        if (method_exists($block, 'getItemCollection')) {
            return $block->getItemCollection();
        }
        return $this->getItems();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->hasItems()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * @return bool
     */
    public function hasItems()
    {
        return !empty($this->getItems());
    }

    /**
     * @return array
     */
    public function getAllItems()
    {
        return $this->getItems();
    }

    /**
     * @return bool
     */
    public function isShuffled()
    {
        $block = $this->getProductsBlock();
        if (method_exists($block, 'isShuffled')) {
            return $block->isShuffled();
        }
        return false;
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        foreach ($this->getItems() as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }
        return $identities;
    }

    /**
     * Find out if some products can be easy added to cart
     *
     * @return bool
     */
    public function canItemsAddToCart()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function getPositionLimit()
    {
        return $this->getItemLimit();
    }

    /**
     * @return bool
     */
    public function getItemLimit()
    {
        return $this->addToCartConfirmationPopupHelper->getPromotionBlockProductsCount();
    }
}
