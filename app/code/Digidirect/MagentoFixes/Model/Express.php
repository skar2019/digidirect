<?php
namespace Digidirect\MagentoFixes\Model;

use Magento\Sales\Model\Order;

/**
 * Class Express
 * @package Digidirect\MagentoFixes\Model
 */
class Express extends \Magento\Paypal\Model\Express
{
    /**
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return \Magento\Paypal\Model\Express
     */
    public function order(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $paypalTransactionData = $this->_checkoutSession->getPaypalTransactionData();
        if (!is_array($paypalTransactionData)) {
            $this->_placeOrder($payment, $amount);
        } else {
            $this->_importToPayment($this->_pro->getApi()->setData($paypalTransactionData), $payment);
        }

        $payment->setAdditionalInformation($this->_isOrderPaymentActionKey, true);
        if ($payment->getIsFraudDetected()) {
            return $this;
        }

        $payment->getOrder()->setActionFlag(Order::ACTION_FLAG_INVOICE, false);

        return $this;
    }
}
