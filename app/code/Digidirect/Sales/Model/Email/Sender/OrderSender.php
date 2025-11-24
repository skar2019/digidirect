<?php
namespace Digidirect\Sales\Model\Email\Sender;

use Magento\Sales\Model\Order;

class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{
    /**
     * Prepare email template with custom logic for shipping method
     *
     * @param Order $order
     * @return void
     */
    protected function prepareTemplate(Order $order)
    {
        // Call parent first to set up default template
        parent::prepareTemplate($order);

        try {
            // Get shipping method from order
            $shippingMethod = $order->getShippingMethod();

            // Use the parent's logger
            $this->logger->info('Digidirect OrderSender: Processing order email', [
                'order_id' => $order->getId(),
                'increment_id' => $order->getIncrementId(),
                'shipping_method' => $shippingMethod
            ]);

            // Check if shipping method is store pickup/collection
            if ($shippingMethod === 'collect_collect') {
                $this->logger->info('Digidirect OrderSender: Switching to collection template', [
                    'order_id' => $order->getId(),
                    'template_id' => 69
                ]);

                $this->templateContainer->setTemplateId(69);
            }
        } catch (\Exception $e) {
            $this->logger->error('Digidirect OrderSender: Error - ' . $e->getMessage());
        }
    }
}
