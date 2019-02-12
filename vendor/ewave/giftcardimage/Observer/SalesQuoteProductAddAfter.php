<?php
namespace Ewave\GiftCardImage\Observer;

use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;
use Ewave\GiftCardImage\Model\QuoteItem;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class SalesQuoteProductAddAfter implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var QuoteItem
     */
    protected $quoteItem;

    /**
     * @param RequestInterface $request
     * @param QuoteItem\Proxy $quoteItem
     */
    public function __construct(
        RequestInterface $request,
        QuoteItem\Proxy $quoteItem
    ) {
        $this->request = $request;
        $this->quoteItem = $quoteItem;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($imageId = (int)$this->request->getParam(GiftCardImageInterface::PUBLIC_KEY)) {
            $items = $observer->getItems();
            foreach ($items as $item) {
                $item->setGiftcardImageId($imageId);
                if ($item->getId()) {
                    $this->quoteItem->saveGiftCardImage($item->getId(), $imageId);
                }
            }
        }
    }
}
