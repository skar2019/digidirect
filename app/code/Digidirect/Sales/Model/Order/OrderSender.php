<?php
namespace Digidirect\Sales\Model\Email\Sender;

use Magento\Sales\Model\Order;

class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{
    protected function prepareTemplate(Order $order)
    {
        //Get Payment Method
        $paymentMethod = $order->getPayment()->getMethod();
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
        $shippingAddress = $cart->getQuote()->getShippingAddress();
        $shippingMethod = $shippingAddress->getShippingMethod();
        echo $this->console_log($shippingMethod);
        
        parent::prepareTemplate($order);

        if ($shippingMethod == 'collect') {
            $this->templateContainer->setTemplateId(15);
        }

    }
    
    function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
    ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }
}