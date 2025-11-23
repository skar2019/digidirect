<?php
namespace Digidirect\Sales\Plugin;

use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order;

class OrderIdentityPlugin
{
    /**
     * Force a custom template for order emails
     */
    public function aroundGetTemplateId(OrderIdentity $subject, callable $proceed)
    {
        $order = $subject->getTemplateVars()['order'] ?? null;

        if ($order instanceof Order) {
            // Example: only for banktransfer
            // $paymentMethod = $order->getPayment()->getMethod();
            // if ($paymentMethod === 'banktransfer') { return 69; }

            return 69; // force template 69 for all orders
        }

        return $proceed();
    }
}
