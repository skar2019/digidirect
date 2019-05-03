<?php

namespace Ewave\CheckoutFields\Observer;

use Ewave\CheckoutFields\Api\QuoteFieldValueManagementInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

/**
 * Class SaveCustomCheckoutValuesBeforePlaceOrder
 *
 * @package Ewave\CheckoutFields\Observer
 */
class SaveCustomCheckoutValuesBeforePlaceOrder implements ObserverInterface
{
    /**
     * @var QuoteFieldValueManagementInterface
     */
    protected $quoteFieldValueManagement;

    /**
     * SaveCustomCheckoutValuesBeforePlaceOrder constructor.
     *
     * @param QuoteFieldValueManagementInterface $quoteFieldValueManagement
     */
    public function __construct(QuoteFieldValueManagementInterface $quoteFieldValueManagement)
    {
        $this->quoteFieldValueManagement = $quoteFieldValueManagement;
    }

    /**
     * Save checkout fields data before order placing
     *
     * @param Observer $observer
     *
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        /** @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        if (!$quote instanceof CartInterface) {
            return $this;
        }

        $this->quoteFieldValueManagement->saveToQuoteFromExtensionAttributes($quote);

        return $this;
    }
}
