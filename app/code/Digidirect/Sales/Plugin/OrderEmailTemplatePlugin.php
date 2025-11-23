<?php
namespace Digidirect\Sales\Plugin;

use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order;

class OrderEmailTemplatePlugin
{
    public function beforeSend(OrderSender $subject, Order $order, $forceSyncMode = null)
    {
        $shippingMethod = $order->getShippingMethod();

        if ($shippingMethod === 'collect_collect') {
            $subject->getTemplateContainer()->setTemplateId(69);
        }

        return [$order, $forceSyncMode];
    }
}
