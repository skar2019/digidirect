<?php

namespace Ewave\AbstractAttributes\Plugin\Magento\Catalog\Model\Product\Attribute;

/**
 * Class OptionManagementPlugin
 */
class OptionManagementPlugin
{
    const SAVE_STRING = 'eaa/option/save';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * OptionManagementPlugin constructor.
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Magento\Framework\UrlInterface $url
    ) {
        $this->url = $url;
    }

    /**
     * @param \Magento\Catalog\Model\Product\Attribute\OptionManagement $object
     * @param $attributeCode
     * @param $option
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function beforeAdd(\Magento\Catalog\Model\Product\Attribute\OptionManagement $object, $attributeCode, $option)
    {
        if (
            strpos($this->url->getCurrentUrl(), self::SAVE_STRING) !== false &&
            !empty($option->getData('store_labels'))
        ) {
            /** @var \Magento\Eav\Api\Data\AttributeOptionInterface[] $currentOptions */
            $currentOptions = $object->getItems($attributeCode);

            if (is_array($currentOptions)) {
                foreach ($currentOptions as $currentLabelValue) {
                    foreach ($option->getData('store_labels') as $optionLabelValue) {
                        if ($currentLabelValue->getLabel() == $optionLabelValue->getLabel()) {
                            $option->setLabel($optionLabelValue->getLabel());
                        }
                    }
                }
            }
        }

        return [$attributeCode, $option];
    }
}
