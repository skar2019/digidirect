<?php
namespace Digidirect\Sales\Model\Email\Sender;

use Magento\Sales\Model\Order;

class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{
    protected function prepareTemplate(Order $order)
    {
        //Get Payment Method
        $paymentMethod = $order->getPayment()->getMethod();
        
        parent::prepareTemplate($order);

        //Define email template for each payment method
        switch ($paymentMethod) {
            case 'banktransfer' :
                $templateId = 'custom_template_cod';
                break;
            // Add cases if you have more payment methods
            default:
                $templateId = $order->getCustomerIsGuest() ? $this->identityContainer->getGuestTemplateId() : $this->identityContainer->getTemplateId();

        }

        $this->templateContainer->setTemplateId(15);
    }
}