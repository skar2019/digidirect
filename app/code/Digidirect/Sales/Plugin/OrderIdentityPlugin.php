<?php
namespace Digidirect\Sales\Plugin;

use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order;

class OrderIdentityPlugin
{
    /**
     * Override template for Click & Collect orders
     *
     * @param OrderIdentity $subject
     * @param callable $proceed
     * @return int|string
     */
    public function aroundGetTemplateId(OrderIdentity $subject, callable $proceed)
    {
        $templateId = $proceed(); // default template

        $vars = $subject->getTemplateVars();
        $order = $vars['order'] ?? null;

        /*if ($order instanceof Order) {
            $shippingMethod = $order->getShippingMethod(); // e.g., clickandcollect_pickup
            if (strpos(strtolower($shippingMethod), 'clickandcollect') !== false) {
                return 69; // Force template ID 69 for C&C
            }
        }

        return $templateId;*/
        
        return 69;
    }
}
