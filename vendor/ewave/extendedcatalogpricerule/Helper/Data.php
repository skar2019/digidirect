<?php
namespace Ewave\ExtendedCatalogPriceRule\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Class Data
 * @package Ewave\ExtendedCatalogPriceRule\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @var \Magento\Framework\Locale\Format
     */
    protected $localeFormat;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Framework\Locale\Format $localeFormat
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Locale\Format $localeFormat
    ) {
        parent::__construct($context);
        $this->pricingHelper = $pricingHelper;
        $this->localeFormat = $localeFormat;
    }

    /**
     * @param float $amount
     * @return float|string
     */
    public function formatDiscountAmountAsHtml($amount)
    {
        return $this->pricingHelper->currency($amount, true, false);
    }

    /**
     * @param float $amount
     * @return float
     */
    public function formatDiscountAmount($amount)
    {
        return $this->localeFormat->getNumber($amount);
    }
}
