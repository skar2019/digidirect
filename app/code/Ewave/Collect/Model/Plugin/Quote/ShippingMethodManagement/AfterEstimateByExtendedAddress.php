<?php

namespace Ewave\Collect\Model\Plugin\Quote\ShippingMethodManagement;

class AfterEstimateByExtendedAddress
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
     * AfterEstimateByExtendedAddress constructor.
     *
     * @param FilterRates $filterRates
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
     * @param \Magento\Quote\Model\ShippingMethodManagement $methodManagement
     * @param \Magento\Quote\Model\Cart\ShippingMethod[] $rates
     * @return \Magento\Quote\Model\Cart\ShippingMethod[]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterEstimateByExtendedAddress(
        \Magento\Quote\Model\ShippingMethodManagement $methodManagement,
        $rates
    ) {
        if ($this->_collectHelper->isCollectEnable()) {
            return $this->_filter->filterRates($rates);
        }

        return $rates;
    }
}
