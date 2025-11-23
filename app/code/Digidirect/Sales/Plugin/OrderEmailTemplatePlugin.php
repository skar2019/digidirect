<?php
namespace Digidirect\Sales\Plugin;

use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class OrderEmailTemplatePlugin
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function beforeSend(
        OrderSender $subject,
        Order $order,
        $forceSyncMode = null
    ) {
        $shippingMethod = $order->getShippingMethod();
        $this->logger->debug('OrderEmailTemplatePlugin: ' . $shippingMethod);

        if ($shippingMethod === 'collect_collect') {
            $subject->getTemplateContainer()->setTemplateId(69);
            $this->logger->debug('Template set to 69');
        }

        return [$order, $forceSyncMode];
    }
}
