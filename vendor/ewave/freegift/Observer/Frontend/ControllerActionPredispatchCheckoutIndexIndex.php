<?php

namespace Ewave\FreeGift\Observer\Frontend;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ControllerActionPredispatchCheckoutIndexIndex implements ObserverInterface
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
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Ewave\FreeGift\Helper\Config
     */
    protected $_configHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_session;

    /**
     * ControllerActionPredispatchCheckoutIndexIndex constructor.
     * @param \Ewave\FreeGift\Model\Cart $giftCart
     * @param \Ewave\FreeGift\Model\Registry $giftRegistry
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Ewave\FreeGift\Helper\Config $configHelper
     * @param \Magento\Checkout\Model\Session $session
     */
    public function __construct(
        \Ewave\FreeGift\Model\Cart $giftCart,
        \Ewave\FreeGift\Model\Registry $giftRegistry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Ewave\FreeGift\Helper\Config $configHelper,
        \Magento\Checkout\Model\Session $session
    ) {
        $this->_giftCart = $giftCart;
        $this->_giftRegistry = $giftRegistry;
        $this->_urlBuilder = $urlBuilder;
        $this->_configHelper = $configHelper;
        $this->_session = $session;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        if (!$this->_configHelper->isRedirectToCart() || $this->_session->getRedirectDoNotNeeded()) {
            return $this;
        }
        $this->_giftRegistry->reset();
        $this->_giftCart->getQuote()->collectTotals();
        $items = $this->_giftCart->getNewFreeGiftItems();
        if (!empty($items)) {
            $redirectUrl = $this->_urlBuilder->getUrl('checkout/cart');
            $observer->getControllerAction()->getResponse()->setRedirect($redirectUrl);
        }

        return $this;
    }
}
