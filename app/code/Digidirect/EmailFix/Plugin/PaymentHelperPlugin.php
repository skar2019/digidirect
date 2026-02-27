<?php

namespace Digidirect\EmailFix\Plugin;

use Magento\Payment\Model\Config;
use Magento\Store\Model\StoreManagerInterface;

class PaymentHelperPlugin
{
    protected $paymentConfig;
    protected $storeManager;

    public function __construct(
        Config $paymentConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->paymentConfig = $paymentConfig;
        $this->storeManager = $storeManager;
    }

    public function afterGetInfoBlockHtml(
        \Magento\Payment\Helper\Data $subject,
        $result,
        \Magento\Payment\Model\InfoInterface $info,
        $storeId
    ) {

        $method = $info->getMethod();

        if (strpos($method, 'braintree') === false) {
            return $result;
        }

        $additionalInfo = $info->getAdditionalInformation();
        $ccType = $info->getCcType();
        $ccLast4 = $info->getCcLast4();

        /*
        |--------------------------------------------------------------------------
        | Resolve Proper Card Label
        |--------------------------------------------------------------------------
        */

        if (!empty($additionalInfo['credit_card_type'])) {
            $ccType = ucwords(str_replace('_', ' ', $additionalInfo['credit_card_type']));
        } elseif ($ccType) {
            $types = $this->paymentConfig->getCcTypes();
            if (isset($types[$ccType])) {
                $ccType = $types[$ccType];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Dynamic Media URL
        |--------------------------------------------------------------------------
        */

        $mediaUrl = $this->storeManager
            ->getStore($storeId)
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        $baseUrl = $mediaUrl . 'wysiwyg/glow-up/emai-template/';

        $html = '';

        /*
        |--------------------------------------------------------------------------
        | Credit Card Block (Visa / MC / Amex)
        |--------------------------------------------------------------------------
        */

        if ($ccLast4) {

            $cardImage = '';

            switch (strtolower($ccType)) {
                case 'visa':
                    $cardImage = 'visa.png';
                    break;

                case 'mastercard':
                case 'master card':
                    $cardImage = 'master.png';
                    break;

                case 'american express':
                case 'amex':
                    $cardImage = 'amex.png';
                    break;

                default:
                    $cardImage = 'visa.png';
            }

            $html .= '
            <div><strong>Credit Card</strong></div>
            <table cellpadding="0" cellspacing="0" style="margin-top:8px;">
                <tr>
                    <td width="40" valign="middle">
                        <img src="' . $baseUrl . $cardImage . '" width="30" alt="' . $ccType . '">
                    </td>
                    <td valign="middle" style="font-weight:bold;">
                        ' . $ccType . '
                    </td>
                    <td valign="middle" style="padding-left: 10px;">
                        ****-' . $ccLast4 . '
                    </td>
                </tr>
            </table>';
        }

        /*
        |--------------------------------------------------------------------------
        | Apple Pay Block
        |--------------------------------------------------------------------------
        */

        elseif (
            isset($additionalInfo['payment_instrument_type']) &&
            $additionalInfo['payment_instrument_type'] === 'apple_pay'
        ) {

            $html .= '
            <table cellpadding="0" cellspacing="0" style="margin-top:8px;">
                <tr>
                    <td width="40" valign="middle">
                        <img src="' . $baseUrl . 'apple-pay.png" width="30" alt="Apple Pay">
                    </td>
                    <td valign="middle" style="font-weight:bold;">
                        Apple Pay
                    </td>
                </tr>';

            if ($ccType && $ccLast4) {
                $html .= '
                <tr>
                    <td></td>
                    <td style="padding-top:4px;">
                        ' . $ccType . ' ****-' . $ccLast4 . '
                    </td>
                </tr>';
            }

            $html .= '</table>';
        }

        /*
        |--------------------------------------------------------------------------
        | PayPal Block
        |--------------------------------------------------------------------------
        */

        elseif (isset($additionalInfo['paypal_payer_email'])) {

            $html .= '
            <table cellpadding="0" cellspacing="0" style="margin-top:8px;">
                <tr>
                    <td width="40" valign="middle">
                        <img src="' . $baseUrl . 'paypal.png" width="30" alt="PayPal">
                    </td>
                    <td valign="middle" style="font-weight:bold;">
                        PayPal
                    </td>
                    <td style="padding-top:4px;">
                        ' . $additionalInfo['paypal_payer_email'] . '
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td style="padding-top:4px;">
                        ' . $additionalInfo['paypal_payer_email'] . '
                    </td>
                </tr>
            </table>';
        }

        else {
            return $result;
        }

        return $html;
    }
}
