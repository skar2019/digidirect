<?php
namespace Ewave\GiftCardImage\Observer;

use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;
use Ewave\GiftCardImage\Model\QuoteItem;
use Magento\Framework\Event\ObserverInterface;

class SalesQuoteItemSaveAfter implements ObserverInterface
{
    /**
     * @var QuoteItem
     */
    protected $quoteItem;

    /**
     * @param QuoteItem\Proxy $quoteItem
     */
    public function __construct(
        QuoteItem\Proxy $quoteItem
    ) {
        $this->quoteItem = $quoteItem;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getItem();
        $giftcardImageId = $item->getGiftcardImageId();
        if (!$giftcardImageId) {
            $giftcardImageId = (int)$item->getBuyRequest()->getData(GiftCardImageInterface::PUBLIC_KEY);
        }

        if ($giftcardImageId) {
            $this->quoteItem->saveGiftCardImage($item->getId(), $giftcardImageId);
        }
    }
}
