<?php

namespace Ewave\Banner\Block\Widget\Helper;

class VideoHelper implements BannerBlockHelperInterface, MediaHelperInterface, CustomAttributesHelperInterface
{
    const VIDEO = 'video';

    /**
     * @var CustomAttributesHelper
     */
    protected $customAttributesHelper;

    /**
     * @var MediaQueryHelper
     */
    protected $mediaHelper;

    /**
     * VideoHelper constructor.
     *
     * @param CustomAttributesHelper $customAttributesHelper
     * @param MediaQueryHelper $mediaHelper
     */
    public function __construct(CustomAttributesHelper $customAttributesHelper, MediaQueryHelper $mediaHelper)
    {
        $this->mediaHelper = $mediaHelper;
        $this->customAttributesHelper = $customAttributesHelper;
    }

    /**
     * @param array $bannerItem
     * @return array
     */
    public function get(array $bannerItem)
    {
        return $this->customAttributesHelper->getCustomSetting($bannerItem, self::VIDEO, []);
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
     * @param array $video
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function getVideoAttribute(array $video, $key, $default = null)
    {
        return isset($video[$key])  ? $video[$key] : $default;
    }
}
