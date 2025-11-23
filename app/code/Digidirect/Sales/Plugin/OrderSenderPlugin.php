<?php
namespace Digidirect\Sales\Plugin;

use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order;

class OrderSenderPlugin
{
    /**
     * Override template for Click & Collect orders
     *
     * @param OrderSender $subject
     * @param callable $proceed
     * @param Order $order
     */
    public function aroundPrepareTemplate(OrderSender $subject, callable $proceed, Order $order)
    {
        $subject->getTemplateContainer()->setTemplateId(69);
        // Continue with original method
        $proceed($order);
    }
}
