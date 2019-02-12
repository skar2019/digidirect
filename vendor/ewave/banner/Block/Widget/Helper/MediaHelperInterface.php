<?php

namespace Ewave\Banner\Block\Widget\Helper;

interface MediaHelperInterface
{
    /**
     * @param array $bannerItem
     * @return mixed
     */
    public function getMediaQuery(array $bannerItem);
}
