<?php
namespace Ewave\Banner\Model\Attributes;

use Magento\Banner\Model\Banner as BannerModel;

interface AttributesInterface
{
    /**
     * @param BannerModel $banner
     * @return mixed
     */
    public function setAttribute(BannerModel $banner);

    /**
     * @param BannerModel $banner
     * @return mixed
     */
    public function getFrontendAttribute(BannerModel $banner);
}
