<?php
namespace Ewave\ProductCalculator\Model\Media;

/**
 * Class Config
 * @package Ewave\ProductCalculator\Model\Media
 */
class Config extends \Magento\Catalog\Model\Product\Media\Config
{
    const VALID_TYPES = ['gif', 'jpeg', 'jpg', 'png', 'svg'];

    const MEDIA_URL = 'productcalculator/field/images';
    const MEDIA_PATH = 'productcalculator/field/images';

    /**
     * {@inheritdoc}
     */
    public function getBaseMediaPathAddition()
    {
        return self::MEDIA_PATH;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseMediaUrlAddition()
    {
        return self::MEDIA_URL;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseMediaPath()
    {
        return self::MEDIA_PATH;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
               . self::MEDIA_URL;
    }
}
