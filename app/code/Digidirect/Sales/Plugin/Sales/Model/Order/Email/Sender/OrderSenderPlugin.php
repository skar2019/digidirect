<?php
namespace Digidirect\Sales\Plugin\Sales\Model\Order\Email\Sender;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\Template;
use Psr\Log\LoggerInterface;

class OrderSenderPlugin
{
    /**
     * @var Template
     */
    protected $templateContainer;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Template $templateContainer
     * @param LoggerInterface $logger
     */
    public function __construct(
        Template $templateContainer,
        LoggerInterface $logger
    ) {
        $this->templateContainer = $templateContainer;
        $this->logger = $logger;
    }

    /**
     * Plugin to modify template based on shipping method
     *
     * @param \Magento\Sales\Model\Order\Email\Sender\OrderSender $subject
     * @param callable $proceed
     * @param Order $order
     * @param bool $forceSyncMode
     * @return bool
     */
    public function aroundSend(
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $subject,
        callable $proceed,
        Order $order,
                                                            $forceSyncMode = false
    ) {
        $this->logger->info('=== Digidirect Order Sender Plugin: Start ===');
        $this->logger->info('Order ID: ' . $order->getId());
        $this->logger->info('Order Increment ID: ' . $order->getIncrementId());

        try {
            // Get shipping method from order
            $shippingMethod = $order->getShippingMethod();

            $this->logger->info('Shipping Method: ' . ($shippingMethod ?: 'NULL'));

            // Check if shipping method is store pickup/collection
            if ($shippingMethod === 'collect_collect') {
                $this->logger->info('Switching to collection template (ID: 69)');
                $this->templateContainer->setTemplateId(69);
            } else {
                $this->logger->info('Using default template (not collection method)');
            }
        } catch (\Exception $e) {
            $this->logger->error('Digidirect Order Sender Plugin Error: ' . $e->getMessage());
            $this->logger->error('Stack trace: ' . $e->getTraceAsString());
        }

        $this->logger->info('=== Digidirect Order Sender Plugin: End ===');

        // Call the original send method
        return $proceed($order, $forceSyncMode);
    }
}
