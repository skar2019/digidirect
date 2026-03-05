<?php

namespace Digidirect\ExtendedShippingRates\Model\Config\Source;

use Digidirect\ExtendedShippingRates\Api\CustomConditionInterface;
use Magento\Framework\Api\ObjectFactory;

/**
 * Class AvailableCustomConditions
 * @package Digidirect\ExtendedShippingRates\Model\Config\Source
 */
class AvailableCustomConditions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var ObjectFactory
     */
    protected $objectFactory;

    /**
     * @var array
     */
    protected $customConditionList;

    /**
     * @param ObjectFactory $objectFactory
     * @param array $customConditionList
     */
    public function __construct(
        ObjectFactory $objectFactory,
        array $customConditionList = []
    ) {
        $this->objectFactory = $objectFactory;
        $this->customConditionList = $this->prepareConditionList($customConditionList);
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        if (!empty($this->getCustomConditionsArray())) {
            foreach ($this->getCustomConditionsArray() as $key => $condition) {
                $options[] = ['value' => $key, 'label' => $condition->getLabel()];
            }
        }
        return $options;
    }

    /**
     * @param array $customConditionList
     * @return array
     */
    public function prepareConditionList($customConditionList)
    {
        $conditionList = [];
        if (!empty($customConditionList)) {
            foreach ($customConditionList as $key => $condition) {
                if (is_string($condition)) {
                    $condition = $this->objectFactory->create(ltrim($condition, '\\'), []);
                }

                if ($condition instanceof CustomConditionInterface) {
                    $conditionList[$key] = $condition;
                }
            }
        }
        return $conditionList;
    }

    /**
     * @return array
     */
    public function getCustomConditionsArray()
    {
        return $this->customConditionList;
    }
}
