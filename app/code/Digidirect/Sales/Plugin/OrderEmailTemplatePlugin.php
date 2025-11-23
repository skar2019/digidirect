<?php
namespace Digidirect\Sales\Plugin;

use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order;

class OrderEmailTemplatePlugin
{
    public function beforeSend(OrderSender $subject, Order $order, $forceSyncMode = null)
    {
        if ($order->getShippingMethod() === 'collect_collect') {

            // Get template container from subject (protected property) via getter
            if (method_exists($subject, 'getTemplateContainer')) {
                $subject->getTemplateContainer()->setTemplateId(69);
            }
        }

        return [$order, $forceSyncMode];
    }
}
