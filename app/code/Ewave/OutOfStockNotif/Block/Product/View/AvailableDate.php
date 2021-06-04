<?php

namespace Ewave\OutOfStockNotif\Block\Product\View;

use Ewave\OutOfStockNotif\Setup\UpgradeData;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonHelper;

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
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * AvailableDate constructor.
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param TimezoneInterface $timezone
     * @param array $data
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        TimezoneInterface $timezone,
        array $data = [],
        JsonHelper $jsonHelper = null
    ) {
        parent::__construct($context, $arrayUtils, $data);
        $this->timezone = $timezone;
        $this->jsonHelper = $jsonHelper ?: ObjectManager::getInstance()->get(JsonHelper::class);
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function isAvailableDateEnable($product)
    {
       return $this->getAttributeDate($product, UpgradeData::ATTRIBUTE_NAME_DISPLAY_AVAILABEL_DATE);
    }

    /**
     * @param Product $product
     * @return string|false
     */
    public function getAvailableDate($product)
    {
        if ($attributeDate = $this->getAttributeDate($product, UpgradeData::ATTRIBUTE_NAME_AVAILABEL_DATE)) {
            return $this->timezone->date(new \DateTime($attributeDate))->format('d-m-y g:i A T');
        }
        return false;
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function isShowMessage($product)
    {
        if (!$product->getIsSalable() && ($this->isAvailableDateEnable($product) || $this->isSoldOutEnable($product))) {
            return true;
        }
        return false;
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function isSoldOutEnable($product)
    {
        return $this->getAttributeDate($product, UpgradeData::ATTRIBUTE_NAME_SOLD_OUT_MESSAGE);
    }

    /**
     * @param Product $product
     * @param string $attributeName
     * @return string|false
     */
    public function getAttributeDate($product, $attributeName)
    {
        if ($attribute = $product->getCustomAttribute($attributeName)) {
            return $attribute->getValue();
        }
        return false;
    }

    /**
     * @param array $products
     * @return string
     */
    public function getAvailabilityDateInfo(array $products)
    {
        $info = [];
        foreach ($products as $product) {
            if ($this->isAvailableDateEnable($product) && ($availableDate = $this->getAvailableDate($product))) {
                $info[$product->getId()] = $availableDate;
            }
        }
        return $this->jsonHelper->serialize($info);
    }

    /**
     * @param array $products
     * @return string
     */
    public function getSoldOutMessageInfo(array $products)
    {
        $info = [];
        foreach ($products as $product) {
            if ($this->isSoldOutEnable($product)) {
                $info[] = $product->getId();
            }
        }
        return $this->jsonHelper->serialize($info);
    }
}
