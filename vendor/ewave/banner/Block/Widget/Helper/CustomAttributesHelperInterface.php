<?php

namespace Ewave\Banner\Block\Widget\Helper;

/**
 * Custom attributes particular setting getter
 *
 * @since 3.0.0
 */
interface CustomAttributesHelperInterface
{
    /**
     * @param array $bannerItem
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function getCustomSetting(array $bannerItem, $key, $default = null);
}
