<?php
namespace Digidirect\Sales\Plugin;

class OrderEmailTemplatePlugin
{
    public function beforeSend(
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $subject,
        \Magento\Sales\Model\Order $order,
        $forceSyncMode = false
    ) {
        // get shipping method
        $shippingMethod = $order->getShippingMethod();

        if ($shippingMethod === 'collect_collect') {
            // Override the template before email is prepared
            $subject->setTemplateId(69);
        }

        return [$order, $forceSyncMode];
    }
}
