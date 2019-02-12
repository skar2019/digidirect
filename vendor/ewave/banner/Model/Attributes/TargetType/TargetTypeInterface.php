<?php
namespace Ewave\Banner\Model\Attributes\TargetType;

use Magento\Banner\Model\Banner as BannerModel;

interface TargetTypeInterface
{
    /**
     * @param BannerModel $banner
     * @return mixed
     */
    public function getBackendAttributeForSave(BannerModel $banner);

    /**
     * @param int|string $link
     * @return mixed
     */
    public function getUrl($link);

    /**
     * @param BannerModel $banner
     * @param null $current
     * @return mixed
     */
    public function getBackendAttributeForEdit(BannerModel $banner, $current = null);

    /**
     * @param int $entityId
     * @return string|null
     */
    public function getValue($entityId);
}
