<?php

namespace Ewave\PreOrder\Helper;

/**
 * Class Templater
 *
 * @package Ewave\PreOrder\Helper
 */
class Templater extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * Templater constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->timezone = $timezone;

        parent::__construct($context);
    }

    /**
     * Process template
     *
     * @param string $template
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function process($template, \Magento\Catalog\Model\Product $product)
    {
        $this->product = $product;
        $result = preg_replace_callback('/\{([^\{\}]+)\}/', [$this, 'attributeReplaceCallback'], $template);
        return $result;
    }

    /**
     * Attribute replace callback
     *
     * @param array $match
     * @return string
     */
    protected function attributeReplaceCallback(array $match)
    {
        $attributeCode = $match[1];
        $value = $this->product->getData($attributeCode);
        if (null === $value) {
            $value = $this->product->getResource()->getAttributeRawValue(
                $this->product->getId(),
                $attributeCode,
                $this->product->getStoreId()
            );
        }

        if (is_array($value)) {
            $value = isset($value[$attributeCode]) ? $value[$attributeCode] : null;
        }

        $attributes = $this->product->getResource()->getAttributesByCode();
        if (isset($attributes[$attributeCode])) {
            /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
            $attribute = $attributes[$attributeCode];
            $frontend = $attribute->getFrontendInput();
            if ('select' == $frontend) {
                $value = $attribute->getSource()->getOptionText($value);
            } else {
                if ('date' == $frontend) {
                    try {
                        // Avoid timezone offset issue
                        $date = new \Zend_Date($value);
                        $value = $this->timezone->formatDate($date, \IntlDateFormatter::MEDIUM, false);
                    } catch (\Zend_Date_Exception $e) {
                        $value = '';
                    }
                }
            }
        }

        return $value ?: '';
    }
}
