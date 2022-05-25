<?php

namespace Marketplacer\SellerApi\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Marketplacer\SellerApi\Model\Order\SellerDataPreparer;
use Marketplacer\SellerApi\Api\Data\OrderInterface;

class ConvertQuoteToOrder implements ObserverInterface
{
    /**
     * @var SellerDataPreparer
     */
    protected $sellerDataPreparer;

    /**
     * ConvertQuoteToOrder constructor.
     * @param SellerDataPreparer $sellerDataPreparer
     */
    public function __construct(
        SellerDataPreparer $sellerDataPreparer
    ) {
        $this->sellerDataPreparer = $sellerDataPreparer;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Quote\Model\Quote $quote
         * @var \Magento\Sales\Model\Order $order
         */

        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        $items = $quote->getAllVisibleItems();
        $sellerIds = $this->sellerDataPreparer->getSellerIdsByQuoteItems($items);
        $sellerNames = $this->sellerDataPreparer->getSellerNamesByIds($sellerIds, $order->getStoreId());

        $order->setData(OrderInterface::SELLER_IDS, implode(',', $sellerIds));
        $order->setData(OrderInterface::SELLER_NAMES, implode(', ', $sellerNames));
        return $this;
    }
}
