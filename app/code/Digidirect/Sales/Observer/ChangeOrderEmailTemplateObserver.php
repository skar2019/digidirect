<?php
namespace Digidirect\Sales\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class ChangeOrderEmailTemplateObserver implements ObserverInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $transport = $observer->getData('transportObject');
        if (!$transport) {
            return;
        }

        $order = $transport->getData('order');
        if (!$order instanceof Order) {
            return;
        }

        $shippingMethod = $order->getShippingMethod();

        // Override the template ID directly in the transport
        $transport->setData('template_id', 69);

        // Optional: logging for debugging
        $this->logger->info('Click & Collect template applied via transport', [
            'order_id' => $order->getIncrementId(),
            'shipping_method' => $shippingMethod,
            'template_id' => 69
        ]);
    }
}
