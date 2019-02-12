<?php

namespace Ewave\MagentoFixes\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Ewave\MagentoFixes\Model\Adminhtml\Paypal\Express;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Validation\CanInvoice;

/**
 * Class ValidatorCanInvoice
 * Decorates order canInvoice validator method for PayPal Express payments
 * when payment action set to "Order".
 * @package Ewave\MagentoFixes\Plugin
 */
class ValidatorCanInvoice
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
     * @param CanInvoice $subject
     * @param $result
     * @param OrderInterface $order
     * @return array
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterValidate(CanInvoice $subject, $result, OrderInterface $order): array
    {
        if ($this->express->isOrderAuthorizationAllowed($order->getPayment())) {
            $result[] = __('An invoice cannot be created when none of authorization transactions available.');
        }

        return $result;
    }
}
