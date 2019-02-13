<?php

namespace Ewave\FreeGift\Block;

class Add extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ewave\FreeGift\Model\Cart
     */
    protected $_giftCart;

    /**
     * @var \Ewave\FreeGift\Model\Registry
     */
    protected $_giftRegistry;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Add constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ewave\FreeGift\Model\Cart $giftCart
     * @param \Ewave\FreeGift\Model\Registry $giftRegistry
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ewave\FreeGift\Model\Cart $giftCart,
        \Ewave\FreeGift\Model\Registry $giftRegistry,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_giftCart = $giftCart;
        $this->_giftRegistry = $giftRegistry;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $this->_giftRegistry->reset();
        $this->_giftCart->getQuote()->collectTotals();
        $items = $this->_giftCart->getNewFreeGiftItems();
        if (count($items)) {
            $this->_checkoutSession->setRedirectDoNotNeeded(false);
            return parent::_toHtml();
        } else {
            $this->_checkoutSession->setRedirectDoNotNeeded(true);
        }

        return '';
    }
}
