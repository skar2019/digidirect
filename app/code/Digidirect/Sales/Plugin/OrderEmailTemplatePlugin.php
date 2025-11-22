<?php
namespace Digidirect\Sales\Plugin;

use Magento\Sales\Model\Order\Email\Sender\OrderSender;

class OrderEmailTemplatePlugin
{
    public function aroundPrepareTemplate(OrderSender $subject, callable $proceed, $order)
    {
        // First, call the original method to initialize template container
        $proceed($order);

        // Now safely override template
        $shippingMethod = $order->getShippingMethod();
        if ($shippingMethod === 'collect_collect') {
            $subject->getTemplateContainer()->setTemplateId(69);
        }
    }
}
