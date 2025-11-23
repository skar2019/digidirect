<?php
namespace Digidirect\Sales\Plugin;

use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Order;

class OrderSenderPlugin
{
    /**
     * Force custom email template ID for order emails
     *
     * @param OrderSender $subject
     * @param callable $proceed
     * @param Order $order
     * @param bool $forceSyncMode
     * @return bool
     */
    public function aroundSend(OrderSender $subject, callable $proceed, Order $order, $forceSyncMode = false)
    {
        // Force template ID 69
        $templateId = 69;

        // Set template ID in OrderIdentity container
        $orderIdentity = $subject->getTemplateContainer();
        if ($orderIdentity) {
            $orderIdentity->setTemplateId($templateId);
        }

        // Continue sending email
        return $proceed($order, $forceSyncMode);
    }
}
