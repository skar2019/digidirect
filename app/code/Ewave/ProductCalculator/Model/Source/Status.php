<?php

namespace Ewave\ProductCalculator\Model\Source;

/**
 * Class Status
 * @package Ewave\ProductCalculator\Model\Source
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    const ACTIVE = 1;
    const INACTIVE = 0;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ACTIVE, 'label' => __('Active')],
            ['value' => self::INACTIVE, 'label' => __('Inactive')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::ACTIVE => __('Active'), self::INACTIVE => __('Inactive')];
    }
}
