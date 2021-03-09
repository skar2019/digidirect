<?php

namespace Digidirect\MagentoFixes\Preference\Magento\Paypal\Model;

/**
 * Class Ipn
 * @package Digidirect\MagentoFixes\Preference\Magento\Paypal\Model
 */
class Ipn extends \Magento\Paypal\Model\Ipn
{
    /**
     * Process payment pending notification
     *
     * @return void
     * @throws Exception
     */
    public function _registerPaymentPending()
    {
        $reason = $this->getRequestData('pending_reason');
        if ('authorization' === $reason) {
            $this->_registerPaymentAuthorization();
            return;
        }

        // case when was placed using PayPal standard
        if (\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT == $this->_order->getState()
            && !$this->getRequestData('transaction_entity')
        ) {
            $this->_registerPaymentCapture();
            return;
        }

        $this->_importPaymentInformation();

        $this->_order->getPayment()->setPreparedMessage(
            $this->_createIpnComment($this->_paypalInfo->explainPendingReason($reason))
        )->setTransactionId(
            $this->getRequestData('txn_id')
        )->setIsTransactionClosed(
            0
        )->update(false);
        $this->_order->save();
    }
}