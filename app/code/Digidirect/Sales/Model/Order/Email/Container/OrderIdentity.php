<?php
namespace Digidirect\Sales\Model\Order\Email\Container;

use Magento\Sales\Model\Order\Email\Container\OrderIdentity as CoreOrderIdentity;

class OrderIdentity extends CoreOrderIdentity
{
    public function getTemplateId()
    {
        $order = $this->getOrder();
        $shippingMethod = $order->getShippingMethod();

        if ($shippingMethod == 'collect_collect') {
            return 69; // your custom template ID
        }

        return parent::getTemplateId();
    }
}
