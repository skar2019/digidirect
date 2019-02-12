<?php

namespace Ewave\MagentoFixes\Preference\Magento\Paypal\Model;

/**
 * Class Pro
 * @package Ewave\MagentoFixes\Preference\Magento\Paypal\Model
 */
class Pro extends \Magento\Paypal\Model\Pro
{
    /**
     * Attempt to capture payment
     * Will return false if the payment is not supposed to be captured
     *
     * @param \Magento\Framework\DataObject $payment
     * @param float $amount
     * @return false|null
     */
    public function capture(\Magento\Framework\DataObject $payment, $amount)
    {
        $authTransactionId = $this->_getParentTransactionId($payment);
        if (!$authTransactionId) {
            return false;
        }
        $api = $this->getApi()
            ->setAuthorizationId($authTransactionId)
            ->setIsCaptureComplete($payment->isCaptureFinal($amount))
            ->setAmount($amount);

        $order = $payment->getOrder();
        $orderIncrementId = $order->getIncrementId();
        $api->setCurrencyCode($order->getBaseCurrencyCode())
            ->setInvNum($orderIncrementId)
            ->setCustref($orderIncrementId)
            ->setPonum($order->getId());

        $api->callDoCapture();
        $this->_importCaptureResultToPayment($api, $payment);
    }
}
