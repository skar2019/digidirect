<?php

namespace Ewave\ExtendedCartPriceRules\Plugin\Magento\SalesRule\Model\Rule\Condition;

use Magento\SalesRule\Model\Rule\Condition\Combine as ConditionCombine;

class Combine
{
    /**
     * @param ConditionCombine $conditionCombine
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetNewChildSelectOptions(ConditionCombine $conditionCombine, $result)
    {
        $result[] = [
            'label' => __('Previous Customer Orders'),
            'value' => \Ewave\ExtendedCartPriceRules\Model\Rule\Condition\OrdersCount::class,
        ];
        $result[] = [
            'label' => __('Previous Customer Order Status'),
            'value' => \Ewave\ExtendedCartPriceRules\Model\Rule\Condition\LastOrderStatus::class,
        ];
        $result[] = [
            'label' => __('Shipping Address'),
            'value' => \Ewave\ExtendedCartPriceRules\Model\Rule\Condition\ShippingAddress::class,
        ];
        $result[] = [
            'label' => __('Product was bought in'),
            'value' => \Ewave\ExtendedCartPriceRules\Model\Rule\Condition\ProductWasBoughtIn::class,
        ];
        $result[] = [
            'label' => __('Product quantity in Source'),
            'value' => \Ewave\ExtendedCartPriceRules\Model\Rule\Condition\ProductQuantityInSource::class,
        ];

        return $result;
    }
}
