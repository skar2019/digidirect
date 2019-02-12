<?php

namespace Ewave\ExtendedCart\Block\Checkout;

class AddToCartConfirmationPopup extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * AddToCartConfirmationPopup constructor.
     *
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Block\Product\Context $context,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->cart = $cart;
    }

    /**
     * @return \Magento\Checkout\Model\Cart
     */
    public function getCart()
    {
        return $this->cart;
    }
}
