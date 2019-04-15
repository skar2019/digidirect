<?php

namespace Ewave\Newsletter\Model\Config;

use Magento\Checkout\Model\Config;

/**
 * Class StoreFrontFields
 * @package Ewave\Newsletter\Model\Config
 */
class StoreFrontFields implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * StoreFrontFields constructor.
     * @param array $fields
     */
    public function __construct(array $fields = [])
    {
        $this->fields = $fields;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->fields as $field) {
            if (is_array($field) && isset($field['value'], $field['label'])) {
                $result[] = ['value' => $field['value'], 'label' => __($field['label'])];
            }
        }

        return $result;
    }
}
