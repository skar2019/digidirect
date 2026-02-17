<?php

namespace Digidirect\EmailFix\Plugin;

class OrderEmailPlugin
{
    public function beforeSend(
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $subject,
        \Magento\Sales\Model\Order $order,
        $forceSyncMode = false
    ) {
        if ($order->getPayment()->getMethod() === 'banktransfer') {
            $order->setData('is_banktransfer', true);
        } else {
            $order->setData('is_banktransfer', false);
        }

        // redeploy

        return [$order, $forceSyncMode];
    }
}
