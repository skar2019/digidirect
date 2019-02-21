<?php
namespace Ewave\ExtendedCart\Block\Checkout;

/**
 * Class AddToCartConfirmationPopup
 *
 * @package Ewave\ExtendedCart\Block\Checkout
 */
class AddToCartConfirmationPopup extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Block alias fallback
     */
    const DEFAULT_TYPE = 'default';

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Quote\Model\Quote\Item
     */
    protected $item;

    /**
     * @var \Ewave\ExtendedCart\Helper\AddToCartConfirmationPopup
     */
    protected $addToCartConfirmationPopupHelper;

    /**
     * AddToCartConfirmationPopup constructor.
     *
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Ewave\ExtendedCart\Helper\AddToCartConfirmationPopup $addToCartConfirmationPopupHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Block\Product\Context $context,
        \Ewave\ExtendedCart\Helper\AddToCartConfirmationPopup $addToCartConfirmationPopupHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );

        $this->cart = $cart;
        $this->addToCartConfirmationPopupHelper = $addToCartConfirmationPopupHelper;
    }

    /**
     * @return \Magento\Checkout\Model\Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @return bool|\Magento\Quote\Model\Quote\Item
     */
    public function getItem()
    {
        if (null === $this->item) {
            $this->item = $this->cart->getQuote()->getItemByProduct($this->getProduct());
        }

        return $this->item;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @return \Magento\Catalog\Block\Product\Image|\Magento\Catalog\Helper\Image
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductImage($product, $imageId)
    {
        $item = $this->getItem();
        if (!$item) {
            return $this->getImage($product, $imageId);
        }

        /** @var \Magento\Checkout\Block\Cart\Item\Renderer $renderer */
        $renderer = $this->getItemRenderer($product->getTypeId());
        $renderer->setItem($item);
        $product = $renderer->getProductForThumbnail();

        return $renderer->getImage($product, $imageId);
    }

    /**
     * Retrieve item renderer block
     *
     * @param string|null $type
     * @return \Magento\Framework\View\Element\Template
     * @throws \RuntimeException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItemRenderer($type = null)
    {
        if ($type === null) {
            $type = self::DEFAULT_TYPE;
        }
        $rendererList = $this->_getRendererList();
        if (!$rendererList) {
            throw new \RuntimeException('Renderer list for block "' . $this->getNameInLayout() . '" is not defined');
        }
        $overriddenTemplates = $this->getOverriddenTemplates() ?: [];
        $template = isset($overriddenTemplates[$type]) ? $overriddenTemplates[$type] : $this->getRendererTemplate();

        return $rendererList->getRenderer($type, self::DEFAULT_TYPE, $template);
    }

    /**
     * Retrieve renderer list
     *
     * @return \Magento\Framework\View\Element\RendererList
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getRendererList()
    {
        return $this->getRendererListName() ? $this->getLayout()->getBlock(
            $this->getRendererListName()
        ) : $this->getChildBlock(
            'renderer.list'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if ($this->addToCartConfirmationPopupHelper->getIsEnabled() && $this->getItem()) {
            return parent::_toHtml();
        }

        return '';
    }
}
