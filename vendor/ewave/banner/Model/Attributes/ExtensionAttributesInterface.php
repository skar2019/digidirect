<?php
namespace Ewave\Banner\Model\Attributes;

use Magento\Banner\Model\Banner as BannerModel;

interface ExtensionAttributesInterface
{
    /**
     * @param BannerModel $banner
     * @return mixed
     */
    public function saveAttribute(BannerModel $banner);
}
