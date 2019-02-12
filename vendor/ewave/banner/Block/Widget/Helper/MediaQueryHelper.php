<?php

namespace Ewave\Banner\Block\Widget\Helper;

class MediaQueryHelper implements BannerBlockHelperInterface
{
    const MEDIA_QUERY = 'media_query';

    /**
     * @var CustomAttributesHelper
     */
    protected $customAttributesHelper;

    /**
     * ImageHelper constructor.
     *
     * @param CustomAttributesHelper $customAttributesHelper
     */
    public function __construct(CustomAttributesHelper $customAttributesHelper)
    {
        $this->customAttributesHelper = $customAttributesHelper;
    }

    /**
     * @param array $bannerItem
     * @return array
     */
    public function get(array $bannerItem)
    {
        $customAttributes = $this->customAttributesHelper->get($bannerItem);
        return isset($customAttributes[static::MEDIA_QUERY]) ? $customAttributes[static::MEDIA_QUERY] : [];
    }
}
