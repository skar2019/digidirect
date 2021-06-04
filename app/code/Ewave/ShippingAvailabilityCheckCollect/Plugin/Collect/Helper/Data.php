<?php

namespace Ewave\ShippingAvailabilityCheckCollect\Plugin\Collect\Helper;

/**
 * Class Data
 * @package Ewave\ShippingAvailabilityCheckCollect\Plugin\Collect\Helper
 */
class Data
{
    /**
     * @var bool
     */
    protected $shippingAvailabilityCheckFlag = false;

    /**
     * @var \Ewave\Collect\Model\Plugin\Quote\ShippingMethodManagement\FilterRates
     */
    protected $filterRates;

    /**
     * Data constructor.
     * @param \Ewave\Collect\Model\Plugin\Quote\ShippingMethodManagement\FilterRates $filterRates
     */
    public function __construct(
        \Ewave\Collect\Model\Plugin\Quote\ShippingMethodManagement\FilterRates $filterRates
    ) {
        $this->filterRates = $filterRates;
    }

    /**
     * @param \Ewave\Collect\Helper\Data $subject
     * @param bool $result
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsAreaForActiveQuote(\Ewave\Collect\Helper\Data $subject, $result)
    {
        if ($this->shippingAvailabilityCheckFlag) {
            $result = false;
        }
        return $result;
    }

    /**
     * @param \Ewave\ShippingAvailabilityCheck\Model\QuoteManagement $subject
     * @param |Magento\Quote\Model\Quote $quote
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetExistedOrCreateNewQuote(
        \Ewave\ShippingAvailabilityCheck\Model\QuoteManagement $subject,
        $quote
    ) {
        $this->shippingAvailabilityCheckFlag = true;
        $this->filterRates->setQuote($quote);
        return $quote;
    }
}
