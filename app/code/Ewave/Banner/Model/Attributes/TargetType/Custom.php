<?php
namespace Ewave\Banner\Model\Attributes\TargetType;

use Magento\Banner\Model\Banner as BannerModel;

class Custom implements TargetTypeInterface
{
    const TARGET_TYPE_REQUEST_CODE = 'custom_link';

    /**
     * @param BannerModel $banner
     * @return mixed
     */
    public function getBackendAttributeForSave(BannerModel $banner)
    {
        return $banner->getData(self::TARGET_TYPE_REQUEST_CODE);
    }

    /**
     * @param int|string $link
     * @return int|string
     */
    public function getUrl($link)
    {
        return $link;
    }

    /**
     * @param int $entityId
     * @return int|string|null
     */
    public function getValue($entityId)
    {
        return $entityId;
    }

    /**
     * @param BannerModel $banner
     * @param null $currentAttribute
     * @return mixed|null
     */
    public function getBackendAttributeForEdit(BannerModel $banner, $currentAttribute = null)
    {
        if ($currentAttribute && $currentAttribute == self::TARGET_TYPE_REQUEST_CODE) {
            $targetId = $banner->getData('target_id');
            $banner->setData(self::TARGET_TYPE_REQUEST_CODE, $targetId);
            return $targetId;
        }
        return null;
    }
}
