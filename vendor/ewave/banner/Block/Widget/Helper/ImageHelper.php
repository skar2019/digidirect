<?php

namespace Ewave\Banner\Block\Widget\Helper;

/**
 * Images properties getter
 *
 * @since 3.0.0
 */
class ImageHelper implements BannerBlockHelperInterface, MediaHelperInterface, CustomAttributesHelperInterface
{
    const IMAGES = 'images';

    /**
     * @var CustomAttributesHelper
     */
    protected $customAttributesHelper;

    /**
     * @var MediaQueryHelper
     */
    protected $mediaHelper;

    /**
     * ImageHelper constructor.
     *
     * @param CustomAttributesHelper $customAttributesHelper
     * @param MediaQueryHelper $mediaHelper
     */
    public function __construct(
        CustomAttributesHelper $customAttributesHelper,
        MediaQueryHelper $mediaHelper
    ) {
        $this->mediaHelper = $mediaHelper;
        $this->customAttributesHelper = $customAttributesHelper;
    }

    /**
     * @param array $bannerItem
     * @return array
     */
    public function get(array $bannerItem)
    {
        return $this->customAttributesHelper->getCustomSetting($bannerItem, static::IMAGES, []);
    }

    /**
     * @param array $bannerItem
     * @return mixed
     */
    public function getMediaQuery(array $bannerItem)
    {
        return $this->mediaHelper->get($bannerItem);
    }

    /**
     * @param array $bannerItem
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function getCustomSetting(array $bannerItem, $key, $default = null)
    {
        return $this->customAttributesHelper->getCustomSetting($bannerItem, $key, $default);
    }

    /**
     * @param array $image
     * @param string $code
     * @param null $default
     * @return mixed|null
     */
    public function getImageAttribute(array $image, $code, $default = null)
    {
        return isset($image[$code]) ? $image[$code] : $default;
    }
}
