<?php
namespace Digidirect\Sales\Plugin;

use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order;

class OrderIdentityPlugin
{
    public function aroundGetTemplateId(OrderIdentity $subject, callable $proceed)
    {
        $order = $subject->getTemplateVars()['order'] ?? null;

        /*if ($order instanceof Order) {
            $shippingMethod = $order->getShippingMethod();

            if ($shippingMethod === 'collect_collect') {
                return 69; // your custom template ID
            }
        }*/

        return 69;
    }
}
