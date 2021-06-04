<?php

namespace Ewave\ProductCalculator\Model\Source\FieldGroup;

/**
 * Class Status
 * @package Ewave\ProductCalculator\Model\Source\FieldGroup
 */
class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'checkbox', 'label' => __('Checkbox')], ['value' => 'radio', 'label' => __('Radio')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['checkbox' => __('Checkbox'), 'radio' => __('Radio')];
    }
}
