<?php

namespace Ewave\Newsletter\Model\Config;

/**
 * Class CustomerAttributes
 *
 * @package Ewave\Newsletter\Model\Config
 */
class CustomerAttributes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    protected $customerMetadata;

    /**
     * StoreFrontFields constructor.
     *
     * @param \Magento\Customer\Api\CustomerMetadataInterface $customerMetadata
     */
    public function __construct(\Magento\Customer\Api\CustomerMetadataInterface $customerMetadata)
    {
        $this->customerMetadata = $customerMetadata;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        $result = [];

        foreach ($this->customerMetadata->getAllAttributesMetadata() as $attributeMetadata) {
            $result[] = [
                'value' => $attributeMetadata->getAttributeCode(),
                'label' => $attributeMetadata->getFrontendLabel()
            ];
        }

        return $result;
    }
}
