<?php

namespace Ewave\Collect\Model\Plugin\Quote;

class Address
{
    /**
     * Filter
     *
     * @var FilterRates
     */
    protected $_filter;

    /**
     * Collect Helper
     *
     * @var \Ewave\Collect\Helper\Data
     */
    protected $_collectHelper;

    /**
     * Address constructor.
     * @param ShippingMethodManagement\FilterRates $filterRates
     * @param \Ewave\Collect\Helper\Data $collectHelper
     */
    public function __construct(
        \Ewave\Collect\Model\Plugin\Quote\ShippingMethodManagement\FilterRates $filterRates,
        \Ewave\Collect\Helper\Data $collectHelper
    ) {
        $this->_filter = $filterRates;
        $this->_collectHelper = $collectHelper;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param [] $rates
     * @return []
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetGroupedAllShippingRates(
        \Magento\Quote\Model\Quote\Address $address,
        $rates
    ) {
        if ($this->_collectHelper->isCollectEnable()) {
            return $this->_filter->filterRates($rates);
        }

        return $rates;
    }
}
