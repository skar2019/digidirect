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
        if ($info->getMethod() !== 'braintree') {
            return $result;
        }

        $ccType = $info->getCcType();
        $ccLast4 = $info->getCcLast4();

        $html = '<div><strong>Credit Card</strong></div>';

        if ($ccType) {
            $html .= '<div><strong>Credit Card Type:</strong> ' . $ccType . '</div>';
        }

        if ($ccLast4) {
            $html .= '<div><strong>Credit Card Number:</strong> ****-' . $ccLast4 . '</div>';
        }

        return $html;
    }
}