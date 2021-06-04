<?php

namespace Ewave\Banner\Block\Widget\StaticWidget;

use Ewave\Banner\Block\Widget\Banner;

interface TypeRendererInterface
{
    const TEMPLATE = 'template';
    const CLASS_NAME = 'class';

    /**
     * @since 3.1.0
     */
    const DEFAULT_STATIC_TEMPLATE = 'Ewave_Banner::widget/static/banner.phtml';

    /**
     * @param Banner $bannerWidget
     * @return TypeRendererInterface
     */
    public function setWidget(Banner $bannerWidget);

    /**
     * @param array $banner
     * @return TypeRendererInterface
     */
    public function setBanner(array $banner = []);

    /**
     * @return string
     */
    public function renderBanner();

    /**
     * @return Banner
     */
    public function getWidget();

    /**
     * @return array
     */
    public function getBanner();

    /**
     * @param string $template
     * @return TypeRendererInterface
     */
    public function setBannerTemplate($template);
}
