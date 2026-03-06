<?php
namespace Digidirect\Sales\Model\Email\Sender;

use Magento\Sales\Model\Order;

class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{
    protected function prepareTemplate(Order $order)
    {
        $shippingMethod = $order->getShippingMethod();
        
        parent::prepareTemplate($order);

        if ($shippingMethod == 'collect_collect') {
            $this->templateContainer->setTemplateId(69);
        }

    }
}
