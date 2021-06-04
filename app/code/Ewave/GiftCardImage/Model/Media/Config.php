<?php
namespace Ewave\GiftCardImage\Model\Media;

/**
 * Class Config
 * @package Ewave\GiftCardImage\Model\Media
 */
class Config extends \Magento\Catalog\Model\Product\Media\Config
{
    const VALID_TYPES = ['gif', 'jpeg', 'jpg', 'png'];

    const MEDIA_URL = 'ewave/giftcatdimages';
    const MEDIA_PATH = 'ewave/giftcatdimages';

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
