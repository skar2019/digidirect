<?php

namespace Ewave\Banner\Block\Widget\Helper;

/**
 * Helper property getter
 *
 * @since 3.0.0
 */
interface BannerBlockHelperInterface
{
    /**
     * @param array $bannerItem
     * @return mixed
     */
    public function get(array $bannerItem);
}
