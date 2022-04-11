<?php

namespace Marketplacer\Marketplacer\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Marketplacer\Marketplacer\Helper\Data as MarketplacerDataHelper;
use Marketplacer\SellerApi\Api\Data\OrderInterface;
use Marketplacer\SellerApi\Model\Order\SellerDataPreparer;

/**
 * Class ConvertQuoteToOrder
 * @package Marketplacer\Marketplacer\Observer\Sales
 */
class ConvertQuoteToOrder implements ObserverInterface
{
    /**
     * @var SellerDataPreparer
     */
    protected $sellerDataPreparer;

    /**
     * @var MarketplacerDataHelper
     */
    protected $marketplacerDataHelper;

    /**
     * ConvertQuoteToOrder constructor.
     * @param SellerDataPreparer $sellerDataPreparer
     * @param MarketplacerDataHelper $marketplacerDataHelper
     */
    public function __construct(
        SellerDataPreparer $sellerDataPreparer,
        MarketplacerDataHelper $marketplacerDataHelper
    ) {
        $this->sellerDataPreparer = $sellerDataPreparer;
        $this->marketplacerDataHelper = $marketplacerDataHelper;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Quote\Model\Quote $quote
         * @var \Magento\Sales\Model\Order $order
         */

        $order = $observer->getEvent()->getOrder();
        $storeId = $order->getStoreId();
        $quote = $observer->getEvent()->getQuote();

        $items = $quote->getAllVisibleItems();
        $sellerIds = $this->sellerDataPreparer->getSellerIdsByQuoteItems($items);
        if ($this->marketplacerDataHelper->displaySellerBusinessNumber($storeId)) {
            $sellerBusinessNumbers = $this->sellerDataPreparer->getSellerBusinessNumbersByIds($sellerIds,
                $storeId);
            $order->setData(OrderInterface::SELLER_BUSINESS_NUMBERS, implode(', ', $sellerBusinessNumbers));
        }
        return $this;
    }
}
