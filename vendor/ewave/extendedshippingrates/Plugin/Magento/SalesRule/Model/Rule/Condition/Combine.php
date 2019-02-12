<?php
namespace Ewave\ExtendedShippingRates\Plugin\Magento\SalesRule\Model\Rule\Condition;

use Magento\SalesRule\Model\Rule\Condition\Combine as Subject;
use Ewave\ExtendedShippingRates\Model\Rule\Condition\UseZoneFromState;
use Ewave\ExtendedShippingRates\Model\Rule\Condition\UseCustomCondition;

/**
 * Class Combine
 * @package Ewave\ExtendedShippingRates\Plugin\Magento\SalesRule\Model\Rule\Condition
 */
class Combine
{
    /**
     * @param \Magento\SalesRule\Model\Rule\Condition\Combine $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetNewChildSelectOptions(Subject $subject, $result)
    {
        $result[] = [
            'label' => __('Use Zones Form State'),
            'value' => UseZoneFromState::class,
        ];

        $result[] = [
            'label' => __('Use custom condition <...>'),
            'value' => UseCustomCondition::class,
        ];
        return $result;
    }
}
