<?php
namespace Digidirect\Sales\Plugin;

use Magento\Sales\Model\Order\Email\Container\OrderIdentity;

class OrderEmailTemplatePlugin
{
    public function afterGetTemplateId(OrderIdentity $subject, $result)
    {
        $order = $subject->getOrder();
        if (!$order) {
            return $result;
        }

        // Get shipping method from ORDER (not quote)
        $shippingMethod = $order->getShippingMethod();

        if ($shippingMethod === 'collect_collect') {
            return 69; // your custom template ID
        }

        return $result;
    }
}
