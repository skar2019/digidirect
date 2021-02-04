<?php

namespace  Sparsh\CustomShippingMethod\Model\Config\Source;

/**
 * Class Type
 *
 * @package Sparsh\CustomShippingMethod\Model\Config\Source
 */
class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * ToOptionArray
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'O', 'label' => __('Per Order')],
            ['value' => 'I', 'label' => __('Per Item')]
        ];
    }
}
