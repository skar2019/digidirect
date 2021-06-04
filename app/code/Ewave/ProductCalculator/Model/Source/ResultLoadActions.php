<?php

namespace Ewave\ProductCalculator\Model\Source;

/**
 * Class ResultLoadActions
 * @package Ewave\ProductCalculator\Model\Source
 */
class ResultLoadActions implements \Magento\Framework\Option\ArrayInterface
{
    const LOAD     = 0;
    const REDIRECT = 1;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::LOAD, 'label' => __('Load')],
            ['value' => self::REDIRECT, 'label' => __('Redirect')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::LOAD => __('Load'), self::REDIRECT => __('Redirect')];
    }
}
