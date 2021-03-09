<?php

namespace Digidirect\MagentoFixes\Plugin;

use Digidirect\MagentoFixes\Model\Adminhtml\Paypal\Express;
use Magento\Sales\Model\Order;

/**
 * Class OrderCanInvoice
 * Decorates Order::canInvoice method for PayPal Express payments.
 * @package Digidirect\MagentoFixes\Plugin
 */
class OrderCanInvoice
{
    /**
     * @var Express
     */
    private $express;

    /**
     * Initialize dependencies.
     *
     * @param Express $express
     */
    public function __construct(Express $express)
    {
        $this->express = $express;
    }

    /**
     * Checks a possibility to invoice of PayPal Express payments when payment action is "order".
     *
     * @param Order $order
     * @param $result
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterCanInvoice(Order $order, $result): bool
    {
        if ($this->express->isOrderAuthorizationAllowed($order->getPayment())) {
            return false;
        }

        return $result;
    }
}
