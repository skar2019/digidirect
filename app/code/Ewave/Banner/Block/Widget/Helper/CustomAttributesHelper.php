<?php

namespace Ewave\Banner\Block\Widget\Helper;

use Ewave\Banner\Helper\IssetTrait;

/**
 * Custom Attributes getter
 *
 * @since 3.0.0
 */
class CustomAttributesHelper implements BannerBlockHelperInterface, CustomAttributesHelperInterface
{
    use IssetTrait;

    const CUSTOM_ATTRIBUTES = 'custom_attributes';

    /**
     * @var BannerValueGetter
     */
    protected $bannerValueGetter;

    /**
     * CustomAttributesHelper constructor.
     *
     * @param BannerValueGetter $bannerValueGetter
     */
    public function __construct(BannerValueGetter $bannerValueGetter)
    {
        $this->bannerValueGetter = $bannerValueGetter;
    }

    /**
     * @param array $bannerItem
     * @return array|mixed
     */
    public function get(array $bannerItem)
    {
        return $this->bannerValueGetter->get($bannerItem, static::CUSTOM_ATTRIBUTES, []);
    }

    /**
     * @param array $bannerItem
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function getCustomSetting(array $bannerItem, $key, $default = null)
    {
        $customAttributes = $this->get($bannerItem);
        return $this->getByKey($customAttributes, $key, $default);
    }
}
