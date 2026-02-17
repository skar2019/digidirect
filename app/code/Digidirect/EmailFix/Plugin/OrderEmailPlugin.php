<?php

namespace Digidirect\EmailFix\Plugin;

class OrderEmailPlugin
{
    public function beforeSetTemplateVars(
        \Magento\Sales\Model\Order\Email\Container\OrderIdentity $subject,
        array $vars
    ) {
        if (isset($vars['order'])) {
            $order = $vars['order'];
            $vars['is_banktransfer'] =
                $order->getPayment()->getMethod() === 'banktransfer';
        }

        return [$vars];
    }
}
