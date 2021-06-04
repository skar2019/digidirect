<?php

namespace Ewave\Banner\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * @since 3.0.0
 */
class BannerHelper extends AbstractHelper
{
    const BANNER_WIDGET = \Magento\Banner\Block\Widget\Banner::class;

    /**
     * @param string $widget
     * @return bool
     */
    public function isBannerWidget($widget)
    {
        return $widget === static::BANNER_WIDGET;
    }
}
