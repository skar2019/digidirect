<?php

namespace Digidirect\MyOrderItems\Model\Config\Source;

use Digidirect\MyOrderItems\Helper\Data;
use Magento\Eav\Model\Config;

/**
 * Class Attribute
 * @package Digidirect\MyOrderItems\Model\Config\Source
 */
class Attribute implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * Options array
     *
     * @var array
     */
    protected $options;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Attribute constructor.
     * @param Config $eavConfig
     * @param Data $helper
     */
    public function __construct(
        Config $eavConfig,
        Data $helper
    ) {
        $this->eavConfig = $eavConfig;
        $this->helper = $helper;
    }

    /**
     * @param bool $isMultiselect
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray($isMultiselect = false)
    {
        if (!$this->options) {
            $this->options = $this->getOptions();
        }

        $options = $this->options;
        if (!$isMultiselect) {
            array_unshift($options, ['value' => '', 'label' => __('--Please Select--')]);
        }

        return $options;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     */
    protected function getOptions()
    {
        $entityType = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY);
        $attributes = $this->eavConfig->getAttributes($entityType);
        $options = [];
        foreach ($attributes as $attribute) {
            $options[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getDefaultFrontendLabel()
            ];
        }

        if (!empty($this->helper->getAdditionalAttributes())) {
            foreach ($this->helper->getAdditionalAttributes() as $value => $label) {
                $options[] = [
                    'value' => $value,
                    'label' => $label
                ];
            }
        }

        return $options;
    }
}
