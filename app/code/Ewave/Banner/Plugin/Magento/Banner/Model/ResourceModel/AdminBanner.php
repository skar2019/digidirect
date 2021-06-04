<?php

namespace Ewave\Banner\Plugin\Magento\Banner\Model\ResourceModel;

use Ewave\Banner\Model\Attributes\Video as VideoAttribute;
use Ewave\Banner\Model\Image\ImageSerializer;
use Ewave\Banner\Model\ResourceModel\Attributes;
use Magento\Banner\Model\ResourceModel\Banner as BannerResource;
use Magento\Banner\Model\Banner as BannerModel;

class AdminBanner extends Banner
{
    /**
     * @var \Ewave\Banner\Helper\Image\Config
     */
    protected $mediaConfig;

    /**
     * @var \Ewave\Banner\Model\Attributes
     */
    protected $attributes;

    /**
     * @var \Ewave\Banner\Model\Image\Uploader
     */
    protected $uploader;

    /**
     * @var VideoAttribute
     */
    protected $videoAttribute;

    /**
     * @var ImageSerializer
     */
    protected $imageSerializer;

    /**
     * AdminBanner constructor.
     *
     * @param \Ewave\Banner\Model\Attributes $attributes
     * @param Attributes $attributesRM
     * @param \Ewave\Banner\Helper\Image\Config $config
     * @param \Ewave\Banner\Model\Image\Uploader $uploader
     * @param VideoAttribute $video
     * @param ImageSerializer $imageSerializer
     */
    public function __construct(
        \Ewave\Banner\Model\Attributes $attributes,
        Attributes $attributesRM,
        \Ewave\Banner\Helper\Image\Config $config,
        \Ewave\Banner\Model\Image\Uploader $uploader,
        VideoAttribute $video,
        ImageSerializer $imageSerializer
    ) {
        parent::__construct($attributes, $attributesRM);
        $this->mediaConfig = $config;
        $this->attributes = $attributes;
        $this->uploader = $uploader;
        $this->videoAttribute = $video;
        $this->imageSerializer = $imageSerializer;
    }

    /**
     * After load banner add target_type and target_id
     *
     * @param BannerResource $banner
     * @param \Closure $proceed
     * @param \Magento\Framework\Model\AbstractModel $bannerModel
     * @param int $value
     * @param null|string $field
     * @return BannerResource
     */
    public function aroundLoad(
        BannerResource $banner,
        \Closure $proceed,
        \Magento\Framework\Model\AbstractModel $bannerModel,
        $value,
        $field = null
    ) {
        $result = parent::aroundLoad($banner, $proceed, $bannerModel, $value, $field);
        $bannerModel->setData('images_banner', $this->getImages($bannerModel));
        return $result;
    }

    /**
     * @param int $bannerId
     * @return array
     */
    protected function getBannerAttributes($bannerId)
    {
        return $this->attributesResourceModel->getBannerAttributesById($bannerId);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $banner
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function getImages(\Magento\Framework\Model\AbstractModel $banner)
    {
        $array = [];
        $bannerImages = $banner->getData('images');
        if (!$bannerImages) {
            return $array;
        }

        $images = $this->imageSerializer->unserialize($bannerImages);

        $srcSets = $this->mediaConfig->getAllSrcSets();

        foreach ($srcSets as $roleCode => $roleTitle) {
            if (!isset($images[$roleCode])) {
                continue;
            }
            $array[] = [
                'file' => $images[$roleCode],
                'type' => $roleCode,
                'value_id' => $roleCode,
                'role_code' => $roleCode,
                'size' =>  $this->uploader->getFileSize($images[$roleCode]),
                'widget_type' => $this->mediaConfig->getValue($roleCode, 'widget_code')
            ];

            unset($images[$roleCode]);
        }

        foreach ($images as $key => $image) {
            $array[$key] = [
                'file' => $image,
                'type' => '',
                'value_id' => $key,
                'role_code' => '',
                'size' =>  $this->uploader->getFileSize($image),
                'widget_type' => $this->mediaConfig->getValue($roleCode, 'widget_code')
            ];
        }

        $videos = $this->videoAttribute->getVideos($banner->getId(), true);
        if (!empty($videos)) {
            $array = array_merge($array, $videos);
        }

        return $array;
    }

    /**
     * @param BannerResource $resourceModelBanner
     * @param BannerModel $bannerToDelete
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function beforeDelete(
        BannerResource $resourceModelBanner,
        BannerModel $bannerToDelete
    ) {
        $images = $bannerToDelete->getImages();
        if ($images && $images = $this->imageSerializer->unserialize($images)) {
            foreach ($images as $code => $image) {
                $this->uploader->deleteImage($image);
            }
        }
        return [$bannerToDelete];
    }
}
