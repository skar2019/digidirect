<?php

namespace Ewave\Banner\Block\Widget\Helper;

use Ewave\Banner\Helper\IssetTrait;

/**
 * Root settings getter
 *
 * @since 3.0.0
 */
class BannerValueGetter
{
    use IssetTrait;

    /**
     * @param array $bannerItem
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function get(array $bannerItem, $key, $default = null)
    {
        return $this->getByKey($bannerItem, $key, $default);
    }
}
