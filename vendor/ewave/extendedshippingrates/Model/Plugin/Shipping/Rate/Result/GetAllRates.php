<?php
namespace Ewave\ExtendedShippingRates\Model\Plugin\Shipping\Rate\Result;

class GetAllRates
{
    /**
     * Disable the marked shipping rates. Rates disabling in the
     * @see \Ewave\ExtendedShippingRates\Model\RulesApplier::disableShippingMethod()
     *
     * @param \Magento\Shipping\Model\Rate\Result $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAllRates($subject, $result)
    {
        foreach ($result as $key => $rate) {
            if ($rate->getIsDisabled()) {
                unset($result[$key]);
            }
        }

        return $result;
    }
}
