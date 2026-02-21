<?php

namespace Digidirect\EmailFix\Plugin;

use Magento\Payment\Model\Config;

class PaymentHelperPlugin
{
    protected $paymentConfig;

    /**
     * CDN-hosted payment icon URLs (SVG/PNG, email-safe inline images via data URI or hosted URL)
     * Using publicly available brand logos safe for transactional email use.
     */
    protected $cardIcons = [
        // Braintree ccType codes => icon config
        'visa'             => ['label' => 'Visa',             'icon' => 'visa'],
        'mastercard'       => ['label' => 'Mastercard',       'icon' => 'mastercard'],
        'master_card'      => ['label' => 'Mastercard',       'icon' => 'mastercard'],
        'amex'             => ['label' => 'American Express',  'icon' => 'amex'],
        'american_express' => ['label' => 'American Express',  'icon' => 'amex'],
        'discover'         => ['label' => 'Discover',          'icon' => 'discover'],
        'jcb'              => ['label' => 'JCB',               'icon' => 'jcb'],
        'diners'           => ['label' => 'Diners Club',       'icon' => 'diners'],
        'diners_club'      => ['label' => 'Diners Club',       'icon' => 'diners'],
        'unionpay'         => ['label' => 'UnionPay',          'icon' => 'unionpay'],
        'union_pay'        => ['label' => 'UnionPay',          'icon' => 'unionpay'],
        'maestro'          => ['label' => 'Maestro',           'icon' => 'maestro'],
    ];

    /**
     * Inline SVG icons for the most common card brands.
     * These are embedded directly so they render in email clients without external requests.
     *
     * Visa, Mastercard, Amex, PayPal, Apple Pay — all as compact inline SVGs.
     */
    protected $svgIcons = [

        'visa' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 750 471" width="46" height="29" style="vertical-align:middle;">
            <rect width="750" height="471" rx="40" ry="40" fill="#1a1f71"/>
            <path d="M278 334l33-198h53l-33 198h-53zm244-193c-10-4-27-8-47-8-52 0-89 27-89 66-1 29 26 45 46 54 20 10 27 16 27 25 0 13-16 19-31 19-21 0-32-3-49-10l-7-3-7 44c12 5 34 10 57 10 55 0 91-27 91-69 0-23-14-40-45-55-19-9-30-15-30-24 0-8 10-17 31-17 18 0 31 4 41 8l5 2 7-42zm136 0h-41c-13 0-22 4-28 17l-79 181h56l11-30h68l6 30h49l-42-198zm-66 127l21-56 6-16 3 15 10 57h-40zm-334-127l-51 135-5-27c-10-32-40-67-74-84l47 176h56l84-200h-57z" fill="#fff"/>
            <path d="M168 136H80l-1 5c69 17 114 59 133 109l-19-95c-3-13-13-17-25-19z" fill="#f9a533"/>
        </svg>',

        'mastercard' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 750 471" width="46" height="29" style="vertical-align:middle;">
            <rect width="750" height="471" rx="40" ry="40" fill="#252525"/>
            <circle cx="281" cy="235" r="150" fill="#eb001b"/>
            <circle cx="469" cy="235" r="150" fill="#f79e1b"/>
            <path d="M375 129a150 150 0 0 1 0 212 150 150 0 0 1 0-212z" fill="#ff5f00"/>
        </svg>',

        'amex' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 750 471" width="46" height="29" style="vertical-align:middle;">
            <rect width="750" height="471" rx="40" ry="40" fill="#2557d6"/>
            <path d="M100 180h550v111H100z" fill="#2557d6"/>
            <text x="375" y="270" font-family="Arial,sans-serif" font-size="90" font-weight="bold" fill="#fff" text-anchor="middle">AMEX</text>
        </svg>',

        'discover' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 750 471" width="46" height="29" style="vertical-align:middle;">
            <rect width="750" height="471" rx="40" ry="40" fill="#fff" stroke="#e0e0e0" stroke-width="2"/>
            <ellipse cx="430" cy="235" rx="120" ry="120" fill="#f76f20"/>
            <text x="180" y="268" font-family="Arial,sans-serif" font-size="72" font-weight="bold" fill="#231f20">DISCOVER</text>
        </svg>',

        'jcb' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 750 471" width="46" height="29" style="vertical-align:middle;">
            <rect width="750" height="471" rx="40" ry="40" fill="#fff" stroke="#e0e0e0" stroke-width="2"/>
            <rect x="110" y="100" width="160" height="271" rx="20" fill="#003087"/>
            <rect x="295" y="100" width="160" height="271" rx="20" fill="#cc0000"/>
            <rect x="480" y="100" width="160" height="271" rx="20" fill="#009f6b"/>
            <text x="190" y="265" font-family="Arial,sans-serif" font-size="80" font-weight="bold" fill="#fff" text-anchor="middle">J</text>
            <text x="375" y="265" font-family="Arial,sans-serif" font-size="80" font-weight="bold" fill="#fff" text-anchor="middle">C</text>
            <text x="560" y="265" font-family="Arial,sans-serif" font-size="80" font-weight="bold" fill="#fff" text-anchor="middle">B</text>
        </svg>',

        'diners' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 750 471" width="46" height="29" style="vertical-align:middle;">
            <rect width="750" height="471" rx="40" ry="40" fill="#fff" stroke="#e0e0e0" stroke-width="2"/>
            <circle cx="290" cy="235" r="140" fill="none" stroke="#004a97" stroke-width="18"/>
            <circle cx="460" cy="235" r="140" fill="none" stroke="#004a97" stroke-width="18"/>
            <text x="375" y="278" font-family="Arial,sans-serif" font-size="46" font-weight="bold" fill="#004a97" text-anchor="middle">DINERS</text>
        </svg>',

        'unionpay' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 750 471" width="46" height="29" style="vertical-align:middle;">
            <rect width="750" height="471" rx="40" ry="40" fill="#e21836"/>
            <rect x="220" y="0" width="310" height="471" rx="0" fill="#fff"/>
            <rect x="430" y="0" width="320" height="471" rx="0" fill="#00447c"/>
            <text x="375" y="278" font-family="Arial,sans-serif" font-size="56" font-weight="bold" fill="#e21836" text-anchor="middle">UnionPay</text>
        </svg>',

        'maestro' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 750 471" width="46" height="29" style="vertical-align:middle;">
            <rect width="750" height="471" rx="40" ry="40" fill="#fff" stroke="#e0e0e0" stroke-width="2"/>
            <circle cx="281" cy="235" r="150" fill="#eb001b" opacity="0.9"/>
            <circle cx="469" cy="235" r="150" fill="#0099df" opacity="0.9"/>
            <path d="M375 129a150 150 0 0 1 0 212 150 150 0 0 1 0-212z" fill="#7b0099" opacity="0.7"/>
        </svg>',

        'paypal' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 750 471" width="46" height="29" style="vertical-align:middle;">
            <rect width="750" height="471" rx="40" ry="40" fill="#fff" stroke="#e0e0e0" stroke-width="2"/>
            <text x="375" y="290" font-family="Arial,sans-serif" font-size="120" font-weight="bold" fill="#003087" text-anchor="middle">Pay</text>
            <text x="530" y="290" font-family="Arial,sans-serif" font-size="120" font-weight="bold" fill="#009cde" text-anchor="middle">Pal</text>
            <text x="375" y="290" font-family="Arial,sans-serif" font-size="120" font-weight="900" fill="#003087" text-anchor="middle">Pay</text>
            <!-- Proper PayPal wordmark -->
            <path d="M180 150h130c55 0 90 28 82 82-10 65-58 96-120 96H235l-15 93H155l25-271zm62 130h42c28 0 50-11 54-40 4-25-12-38-40-38h-43l-13 78z" fill="#003087"/>
            <path d="M260 150h130c55 0 90 28 82 82-10 65-58 96-120 96H315l-15 93H235l25-271zm62 130h42c28 0 50-11 54-40 4-25-12-38-40-38h-43l-13 78z" fill="#009cde"/>
        </svg>',

        'apple_pay' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 750 471" width="46" height="29" style="vertical-align:middle;">
            <rect width="750" height="471" rx="40" ry="40" fill="#000"/>
            <text x="375" y="295" font-family="-apple-system,BlinkMacSystemFont,Arial,sans-serif" font-size="100" fill="#fff" text-anchor="middle"> Pay</text>
            <path d="M224 160c8-10 14-24 12-38-12 1-26 8-34 18-7 9-14 23-12 37 13 1 27-7 34-17z" fill="#fff"/>
            <path d="M236 180c-19-1-35 11-44 11-9 0-23-10-38-10-20 0-38 11-48 29-20 35-5 87 14 115 10 14 21 29 36 29 14 0 20-9 37-9 18 0 23 9 38 9 15 0 25-14 35-28 11-15 15-30 15-31-1 0-29-11-29-44 0-28 22-41 23-42-13-18-33-20-39-20z" fill="#fff"/>
        </svg>',
    ];

    public function __construct(
        Config $paymentConfig
    ) {
        $this->paymentConfig = $paymentConfig;
    }

    /**
     * Wrap an SVG icon in a styled pill/badge container matching the invoice design.
     */
    protected function renderIconBadge(string $svgKey, string $fallbackLabel): string
    {
        $svg = $this->svgIcons[$svgKey] ?? '';

        if ($svg) {
            return '<span style="'
                . 'display:inline-block;'
                . 'border:1px solid #e0e0e0;'
                . 'border-radius:6px;'
                . 'padding:3px 8px;'
                . 'background:#fff;'
                . 'vertical-align:middle;'
                . 'line-height:1;'
                . '">'
                . $svg
                . '</span>';
        }

        // Fallback: plain text label styled as a badge
        return '<span style="'
            . 'display:inline-block;'
            . 'border:1px solid #ccc;'
            . 'border-radius:4px;'
            . 'padding:3px 10px;'
            . 'font-size:12px;'
            . 'font-weight:bold;'
            . 'color:#333;'
            . 'background:#f5f5f5;'
            . 'vertical-align:middle;'
            . '">'
            . htmlspecialchars($fallbackLabel)
            . '</span>';
    }

    /**
     * Resolve the icon key from a raw ccType string coming from Braintree.
     */
    protected function resolveIconKey(string $ccType): string
    {
        $normalised = strtolower(str_replace([' ', '-'], '_', $ccType));

        if (isset($this->cardIcons[$normalised])) {
            return $this->cardIcons[$normalised]['icon'];
        }

        // Partial match — e.g. "visa_debit" should still return visa icon
        foreach ($this->cardIcons as $key => $cfg) {
            if (strpos($normalised, $key) !== false) {
                return $cfg['icon'];
            }
        }

        return '';
    }

    /**
     * Resolve a human-readable card label from ccType.
     */
    protected function resolveCardLabel(string $ccType): string
    {
        $normalised = strtolower(str_replace([' ', '-'], '_', $ccType));

        if (isset($this->cardIcons[$normalised])) {
            return $this->cardIcons[$normalised]['label'];
        }

        foreach ($this->cardIcons as $key => $cfg) {
            if (strpos($normalised, $key) !== false) {
                return $cfg['label'];
            }
        }

        // Last resort: title-case the raw value
        return ucwords(str_replace('_', ' ', $ccType));
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
        $ccType  = $info->getCcType();
        $ccLast4 = $info->getCcLast4();

        // ── Resolve card label ────────────────────────────────────────────────
        if (!empty($additionalInfo['credit_card_type'])) {
            $rawType = $additionalInfo['credit_card_type'];
        } elseif ($ccType) {
            $types = $this->paymentConfig->getCcTypes();
            $rawType = isset($types[$ccType]) ? $types[$ccType] : $ccType;
        } else {
            $rawType = '';
        }

        $html = '';

        // ── CREDIT CARD ───────────────────────────────────────────────────────
        if ($ccLast4) {

            $cardLabel = $rawType ? $this->resolveCardLabel($rawType) : 'Credit Card';
            $iconKey   = $rawType ? $this->resolveIconKey($rawType)   : '';

            $html .= '<div style="margin-bottom:6px;"><strong>Credit Card</strong></div>';

            // Icon badge + label on same line (mirrors the Visa logo in the screenshot)
            $html .= '<div style="margin:4px 0;display:flex;align-items:center;gap:8px;">';

            if ($iconKey) {
                $html .= $this->renderIconBadge($iconKey, $cardLabel);
                $html .= '<span style="font-size:14px;color:#333;">' . htmlspecialchars($cardLabel) . '</span>';
            } else {
                $html .= '<span style="font-size:14px;color:#333;">' . htmlspecialchars($cardLabel) . '</span>';
            }

            $html .= '</div>';
            $html .= '<div style="margin:2px 0;font-size:13px;color:#555;">xxxx-' . htmlspecialchars($ccLast4) . '</div>';
        }

        // ── APPLE PAY ─────────────────────────────────────────────────────────
        elseif (
            isset($additionalInfo['payment_instrument_type']) &&
            $additionalInfo['payment_instrument_type'] === 'apple_pay'
        ) {
            $html .= '<div style="margin-bottom:6px;"><strong>Apple Pay</strong></div>';
            $html .= '<div style="margin:4px 0;display:flex;align-items:center;gap:8px;">';
            $html .= $this->renderIconBadge('apple_pay', 'Apple Pay');
            $html .= '</div>';

            if ($rawType && $ccLast4) {
                $cardLabel = $this->resolveCardLabel($rawType);
                $html .= '<div style="margin:2px 0;font-size:13px;color:#555;">'
                    . htmlspecialchars($cardLabel) . ' xxxx-' . htmlspecialchars($ccLast4)
                    . '</div>';
            }
        }

        // ── PAYPAL ────────────────────────────────────────────────────────────
        elseif (isset($additionalInfo['paypal_payer_email'])) {

            $html .= '<div style="margin-bottom:6px;"><strong>PayPal</strong></div>';
            $html .= '<div style="margin:4px 0;display:flex;align-items:center;gap:8px;">';
            $html .= $this->renderIconBadge('paypal', 'PayPal');
            $html .= '</div>';
            $html .= '<div style="margin:2px 0;font-size:13px;color:#555;">'
                . htmlspecialchars($additionalInfo['paypal_payer_email'])
                . '</div>';
        }

        else {
            return $result;
        }

        return $html;
    }
}