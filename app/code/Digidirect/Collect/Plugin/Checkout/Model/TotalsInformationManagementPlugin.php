<?php
namespace Digidirect\Collect\Plugin\Checkout\Model;

use Magento\Checkout\Model\Session as CheckoutSession;

class TotalsInformationManagementPlugin
{
    protected $checkoutSession;

    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    public function beforeCalculate(
        \Magento\Checkout\Model\TotalsInformationManagement $subject,
                                                            $cartId,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    ) {
        // If no shipping method is provided in the request, clear it from the quote
        if (!$addressInformation->getShippingMethodCode() && !$addressInformation->getShippingCarrierCode()) {
            $quote = $this->checkoutSession->getQuote();
            $quote->getShippingAddress()->setShippingMethod(null);
        }

        return [$cartId, $addressInformation];
    }
}
