<?php
namespace Ewave\ExtendedShippingRates\Model\Rule\Condition;

class Combine extends \Magento\SalesRule\Model\Rule\Condition\Combine
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Ewave\ExtendedShippingRates\Model\Rule\Condition\Address $conditionAddress
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Ewave\ExtendedShippingRates\Model\Rule\Condition\Address $conditionAddress,
        array $data = []
    ) {
        parent::__construct($context, $eventManager, $conditionAddress, $data);
        $this->setType('Ewave\ExtendedShippingRates\Model\Rule\Condition\Combine');
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $addressAttributes = $this->_conditionAddress->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Ewave\ExtendedShippingRates\Model\Rule\Condition\Address|' . $code,
                'label' => $label,
            ];
        }

        $conditions = [
            [
                'value' => '',
                'label' => __('Please choose a condition to add.'),
            ],
            [
                'value' => 'Ewave\ExtendedShippingRates\Model\Rule\Condition\Product\Found',
                'label' => __('Product attribute combination'),
            ],
            [
                'value' => 'Ewave\ExtendedShippingRates\Model\Rule\Condition\Product\Subselect',
                'label' => __('Products subselection'),
            ],
            [
                'value' => 'Ewave\ExtendedShippingRates\Model\Rule\Condition\Combine',
                'label' => __('Conditions combination'),
            ],
            [
                'value' => $attributes,
                'label' => __('Cart Attribute'),
            ],
        ];

        $additional = new \Magento\Framework\DataObject();

        $this->_eventManager->dispatch(
            'shipping_rule_condition_combine',
            ['additional' => $additional]
        );

        $additionalConditions = $additional->getConditions();
        if ($additionalConditions) {
            $conditions = array_merge_recursive(
                $conditions,
                $additionalConditions
            );
        }

        return $conditions;
    }
}
