<?php
namespace Ewave\ExtendedShippingRates\Model\Config\Source;

class WeightType implements \Magento\Framework\Option\ArrayInterface
{
    const FIXED_CODE = 'fixed';
    const PERCENTAGE_CODE = 'percentage';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Select Value')],
            ['value' => self::FIXED_CODE, 'label' => __('Fixed Value')],
            ['value' => self::PERCENTAGE_CODE, 'label' => __('Percentage')]
        ];
    }
}
