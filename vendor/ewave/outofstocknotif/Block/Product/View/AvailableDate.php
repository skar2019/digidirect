<?php

namespace Ewave\OutOfStockNotif\Block\Product\View;

use Ewave\OutOfStockNotif\Setup\UpgradeData;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class AvailableDate
 */
class AvailableDate extends AbstractView
{
    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * AvailableDate constructor.
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param TimezoneInterface $timezone
     * @param array $data
     */
    public function __construct(
       Context $context,
       ArrayUtils $arrayUtils,
       TimezoneInterface $timezone,
        array $data = []
    ) {
        parent::__construct($context, $arrayUtils, $data);
        $this->timezone = $timezone;
    }

    /**
     * @param $product
     * @return mixed
     */
    public function isAvailableDateEnable($product)
    {
       return $this->getAttributeDate($product, UpgradeData::ATTRIBUTE_NAME_DISPLAY_AVAILABEL_DATE);
    }

    /**
     * @param $product
     * @return string
     */
    public function getAvailableDate($product)
    {
        if ($attributeDate = $this->getAttributeDate($product, UpgradeData::ATTRIBUTE_NAME_AVAILABEL_DATE)) {
            return $this->timezone->date(new \DateTime($attributeDate))->format('d-m-y g:i A T');
        }
        return false;
    }

    /**
     * @param $product
     * @return mixed
     */
    public function isShowMessage($product)
    {
        if (!$product->getIsSalable() && ($this->isAvailableDateEnable($product) || $this->isSoldOutEnable($product))) {
            return true;
        }
        return false;
    }

    /**
     * @param $product
     * @return mixed
     */
    public function isSoldOutEnable($product)
    {
        return $this->getAttributeDate($product, UpgradeData::ATTRIBUTE_NAME_SOLD_OUT_MESSAGE);
    }

    /**
     * @param $product
     * @param $attributeName
     * @return mixed
     */
    public function getAttributeDate($product, $attributeName) {
        if ($attribute = $product->getCustomAttribute($attributeName)) {
            return $attribute->getValue();
        }
        return false;
    }
}
