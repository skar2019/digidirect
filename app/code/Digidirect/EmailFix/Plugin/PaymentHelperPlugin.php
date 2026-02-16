<?php

namespace Digidirect\EmailFix\Plugin;

class PaymentHelperPlugin
{
    public function afterGetInfoBlockHtml(
        \Magento\Payment\Helper\Data $subject,
        $result,
        \Magento\Payment\Model\InfoInterface $info,
        $storeId
    ) {
        $method = $info->getMethod();

        // Only modify Braintree methods
        if (strpos($method, 'braintree') === false) {
            return $result;
        }

        $additionalInfo = $info->getAdditionalInformation();
        $ccType = $info->getCcType();
        $ccLast4 = $info->getCcLast4();

        $html = '';

        // ===== Credit Card =====
        if ($ccLast4) {
            $html .= '<div><strong>Credit Card</strong></div>';

            if ($ccType) {
                $html .= '<div><strong>Credit Card Type:</strong> ' . $ccType . '</div>';
            }

            $html .= '<div><strong>Credit Card Number:</strong> ****-' . $ccLast4 . '</div>';
        }

        // ===== Apple Pay =====
        elseif (isset($additionalInfo['payment_instrument_type']) 
            && $additionalInfo['payment_instrument_type'] === 'apple_pay') {

            $html .= '<div><strong>Apple Pay</strong></div>';

            if ($ccType && $ccLast4) {
                $html .= '<div><strong>Card:</strong> ' . $ccType . ' ****-' . $ccLast4 . '</div>';
            }
        }

        // ===== PayPal =====
        elseif (isset($additionalInfo['paypal_payer_email'])) {

            $html .= '<div><strong>PayPal</strong></div>';
            $html .= '<div><strong>PayPal Email:</strong> ' 
                . $additionalInfo['paypal_payer_email'] . '</div>';
        }

        // Fallback (if something unexpected)
        else {
            return $result;
        }

        return $html;
    }
}