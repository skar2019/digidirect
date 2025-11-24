<?php
namespace Digidirect\Sales\Plugin\Sales\Model\Order\Email\Sender;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\Template;
use Psr\Log\LoggerInterface;

class OrderSenderPlugin
{
    protected $templateContainer;
    protected $logger;

    public function __construct(
        Template $templateContainer,
        LoggerInterface $logger
    ) {
        $this->templateContainer = $templateContainer;
        $this->logger = $logger;
    }

    /**
     * Before send
     */
    public function beforeSend(
        $subject,
        Order $order,
        $forceSyncMode = false
    ) {
        $this->logger->info('========================================');
        $this->logger->info('DIGIDIRECT PLUGIN TRIGGERED: beforeSend');
        $this->logger->info('Subject Class: ' . get_class($subject));
        $this->logger->info('Order ID: ' . $order->getId());
        $this->logger->info('========================================');

        $this->modifyTemplate($order);
        return [$order, $forceSyncMode];
    }

    /**
     * Modify template based on shipping method
     */
    protected function modifyTemplate(Order $order)
    {
        try {
            $shippingMethod = $order->getShippingMethod();

            $this->logger->info('Digidirect Plugin - Order Increment ID: ' . $order->getIncrementId());
            $this->logger->info('Digidirect Plugin - Shipping Method: ' . ($shippingMethod ?: 'NULL'));
            $this->logger->info('Digidirect Plugin - Current Template ID: ' . $this->templateContainer->getTemplateId());

            if ($shippingMethod === 'collect_collect') {
                $this->logger->info('Digidirect Plugin - CONDITION MATCHED! Switching to template 69');
                $this->templateContainer->setTemplateId(69);
                $this->logger->info('Digidirect Plugin - New Template ID: ' . $this->templateContainer->getTemplateId());
            } else {
                $this->logger->info('Digidirect Plugin - Condition not matched, keeping default template');
            }
        } catch (\Exception $e) {
            $this->logger->error('Digidirect Plugin Error: ' . $e->getMessage());
            $this->logger->error('Stack Trace: ' . $e->getTraceAsString());
        }
    }
}
