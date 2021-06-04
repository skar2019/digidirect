<?php
namespace Ewave\Collect\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Ewave\Collect\Helper\Data as CollectHelper;

/**
 * Class CartProductAddAfter
 * @package Ewave\Collect\Observer
 */
class CartProductAddAfter implements ObserverInterface
{
    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * CollectHelper
     *
     * @var \Ewave\Collect\Helper\Data
     */
    protected $_collectHelper;

    /**
     * QuoteItemHandler
     *
     * @var \Ewave\Collect\Model\QuoteItemHandler
     */
    protected $_quoteItemHandler;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * SetCollectPlaceData constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param CollectHelper $collectHelper
     * @param \Ewave\Collect\Model\QuoteItemHandler $quoteItemHandler
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Ewave\Collect\Helper\Data $collectHelper,
        \Ewave\Collect\Model\QuoteItemHandler $quoteItemHandler,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_request = $requestInterface;
        $this->_collectHelper = $collectHelper;
        $this->_quoteItemHandler = $quoteItemHandler;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        if (!$this->_collectHelper->isEnableSingleStoreInCartRestriction()) {
            return;
        }
        $item = $observer->getEvent()->getQuoteItem();
        // User tries to add a delivery item while a C&C item exists in the cart.
        if (!$item->getCollectPlaceId() && $this->_collectHelper->hasCollectItemInCart()) {
            $this->transformAllItemsToDelivery();
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function transformAllItemsToDelivery()
    {
        $quote = $this->checkoutSession->getQuote();
        $items = $quote->getAllVisibleItems();
        $updateQuote = false;

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($items as $item) {
            if (!$item->getCollectPlaceId() && !$item->getCollectPlaceStorageName()) {
                continue;
            }
            $item->setCollectPlaceId(null);
            $item->setCollectPlaceStorageName(null);
            $updateQuote = true;
        }
        if ($updateQuote) {
            try {
                $quote->collectTotals();
                $quote->save();
                $quote->collectTotals();
            } catch (\Exception $e) {
                throw new \Exception(__("Cannot convert items to delivery because of {$e->getMessage()}"));
            }
        }
        return true;
    }
}
