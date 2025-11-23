<?php
namespace Digidirect\Sales\Plugin;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Psr\Log\LoggerInterface;

class OrderEmailTemplatePlugin
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function beforeCheckAndSend(OrderSender $subject, Order $order)
    {
        $shippingMethod = $order->getShippingMethod();
        $this->logger->debug('OrderEmailTemplatePlugin (checkAndSend): ' . $shippingMethod);

        if ($shippingMethod === 'collect_collect') {

            // Template container ALWAYS exists here
            $subject->getTemplateContainer()->setTemplateId(69);

            $this->logger->debug('Template set to 69 inside checkAndSend');
        }

        return [$order];
    }
}
