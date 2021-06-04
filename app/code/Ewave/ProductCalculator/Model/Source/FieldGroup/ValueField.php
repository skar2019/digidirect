<?php
namespace Ewave\ProductCalculator\Model\Source\FieldGroup;

use Ewave\ProductCalculator\Api\Data\FieldGroupInterface;

/**
 * Class ValueField
 * @package Ewave\ProductCalculator\Model\Source\FieldGroup
 */
class ValueField implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => FieldGroupInterface::ID, 'label' => __('Id')],
            ['value' => FieldGroupInterface::NAME, 'label' => __('Name')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [FieldGroupInterface::ID => __('Id'), FieldGroupInterface::NAME => __('Name')];
    }
}
