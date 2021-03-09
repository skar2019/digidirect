<?php

namespace Sparsh\CustomShippingMethod\Model\Config\Source;

/**
 * Class HandlingType
 *
 * @package Sparsh\CustomShippingMethod\Model\Config\Source
 */
class HandlingType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'F',
                'label' => __('Fixed'),
            ],
            [
                'value' => 'P',
                'label' => __('Percent')
            ]
        ];
    }
}
