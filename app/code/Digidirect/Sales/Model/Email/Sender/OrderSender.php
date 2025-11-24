<?php
namespace Digidirect\Sales\Model\Email\Sender;

use Magento\Sales\Model\Order;

class OrderSender extends \LatitudeNew\Payment\Model\Order\Email\Sender\OrderSender
{
    /**
     * Override prepareTemplate to set correct template for collection orders
     *
     * @param Order $order
     * @return void
     */
    protected function prepareTemplate(Order $order)
    {
        // Call parent's prepareTemplate first (includes LatitudeNew logic)
        parent::prepareTemplate($order);

        try {
            // Get shipping method
            $shippingMethod = $order->getShippingMethod();

            $this->logger->info('Digidirect prepareTemplate - Order: ' . $order->getIncrementId());
            $this->logger->info('Digidirect prepareTemplate - Shipping: ' . $shippingMethod);
            $this->logger->info('Digidirect prepareTemplate - Template before: ' . $this->templateContainer->getTemplateId());

            // Override template for collection orders
            // This runs AFTER parent, so it takes precedence
            if ($shippingMethod === 'collect_collect') {
                $this->logger->info('Digidirect prepareTemplate - Setting template to 69');
                $this->templateContainer->setTemplateId(69);
                $this->logger->info('Digidirect prepareTemplate - Template after: ' . $this->templateContainer->getTemplateId());
            }
        } catch (\Exception $e) {
            $this->logger->error('Digidirect prepareTemplate Error: ' . $e->getMessage());
        }
    }
}
