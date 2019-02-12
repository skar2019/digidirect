<?php
namespace Ewave\GiftCardImage\Observer;

use Ewave\GiftCardImage\Model\OrderItem;
use Magento\Sales\Model\Order;
use Magento\Framework\Event\ObserverInterface;

class CheckoutSubmitAllAfter implements ObserverInterface
{
    /**
     * @var OrderItem
     */
    protected $orderItem;

    /**
     * @param OrderItem\Proxy $quoteItem
     */
    public function __construct(
        OrderItem\Proxy $quoteItem
    ) {
        $this->orderItem = $quoteItem;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if ($order instanceof Order) {
            $this->orderItem->moveDataFromQuoteToOrder($order);
        }
        return $this;
    }
}
