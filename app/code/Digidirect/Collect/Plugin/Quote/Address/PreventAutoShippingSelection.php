<?php
namespace Digidirect\Collect\Plugin\Quote\Address;

use Magento\Quote\Model\Quote\Address;
use Magento\Checkout\Model\Session as CheckoutSession;

class PreventAutoShippingSelection
{
    protected $checkoutSession;

    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    public function beforeSetShippingMethod(
        Address $subject,
                $method
    ) {
        // Only allow standard_standard if it's been explicitly selected
        // For now, just block it entirely to test
        if ($method === 'standard_standard') {
            return [null];
        }

        return [$method];
    }
}
