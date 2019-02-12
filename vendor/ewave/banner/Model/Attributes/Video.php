<?php

namespace Ewave\Banner\Model\Attributes;

use Ewave\Banner\Model\Image\ImageSerializer;
use Ewave\Banner\Model\ResourceModel\Video as VideoResource;
use Ewave\Banner\Helper\Video\Config as VideoConfigHelper;
use Magento\Framework\UrlInterface;
use Magento\Banner\Model\Banner as BannerModel;
use Ewave\Banner\Model\Attributes\ExtensionAttributes\Video as VideoExtensionAttributes;

/**
 * Class Video
 *
 * @package Ewave\Banner\Model\Attributes
 */
class Video implements AttributesInterface
{
    /**
     * @var VideoResource
     */
    protected $videoResource;

    /**
     * @var VideoConfigHelper
     */
    protected $videoConfigHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var []
     */
    protected $data;

    /**
     * @var null
     */
    protected $imageSrcSets = null;

    /**
     * @var ImageSerializer
     */
    protected $imageSerializer;

    /**
     * @var \Ewave\Banner\Model\Image\Uploader
     */
    protected $uploader;

    /**
     * @since 3.0.0
     * @var array
     */
    protected $bannerAttributesByBannerId = [];

    /**
     * Video constructor.
     *
     * @param VideoResource $video
     * @param VideoConfigHelper $config
     * @param UrlInterface $url
     * @param ImageSerializer $imageSerializer
     * @param \Ewave\Banner\Model\Image\Uploader $uploader
     * @param array $data
     */
    public function __construct(
        VideoResource $video,
        VideoConfigHelper $config,
        UrlInterface $url,
        ImageSerializer $imageSerializer,
        \Ewave\Banner\Model\Image\Uploader $uploader,
        array $data = []
    ) {
        $this->imageSerializer = $imageSerializer;
        $this->videoResource = $video;
        $this->videoConfigHelper = $config;
        $this->urlBuilder = $url;
        $this->data = $data;
        $this->uploader = $uploader;
    }

    /**
     * @param BannerModel $banner
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getFrontendAttribute(BannerModel $banner)
    {
        if (!isset($this->bannerAttributesByBannerId[$banner->getId()])) {
            $bannerVideos = $this->videoResource->getBannerVideos($banner->getId());
            $preparedData = [];
            if (!empty($bannerVideos)) {
                foreach ($bannerVideos as $key => $video) {
                    $roles = $this->explodeRoles($video['video_role_ids']);
                    foreach ($roles as $role) {
                        if (!$role) {
                            continue;
                        }
                        foreach ($video as $fieldKey => $fieldValue) {
                            if ($fieldKey == 'preview_image') {
                                if ($this->isVideoFileWithoutPreviewImage($fieldValue)) {
                                    $preparedData[$key][$fieldKey] = null;
                                    continue;
                                }
                            }
                            $preparedData[$key][$fieldKey] = in_array(
                                $fieldKey,
                                isset($this->data['url_fields']) ? $this->data['url_fields'] : []
                            )
                                ? $this->getEntityUrl($fieldValue) : $fieldValue;
                        }

                        $preparedData[$key]['video_role_ids'] = $this->sortRoles($roles);
                        $preparedData[$key]['media_query'] = $this->sortMedia($roles);
                        $this->excludeImages($banner, $role);
                    }
                }
            }
            $this->bannerAttributesByBannerId[$banner->getId()] = $preparedData;
        }
        return isset($this->bannerAttributesByBannerId[$banner->getId()])
            ? $this->bannerAttributesByBannerId[$banner->getId()]
            : [];
    }

    /**
     * @param null $attribute
     * @param array $roles
     * @param bool $asString
     * @return string
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function sortSrcSetAttribute($attribute = null, array $roles = [], $asString = true)
    {
        $attributes = [];
        foreach ($this->getSrcSetsConfig() as $code) {
            if ($attribute) {
                $attributes[$code] = $this->videoConfigHelper->getImageConfigHelper()->getValue($code, $attribute);
            } else {
                $attributes[$code] = $code;
            }
        }

        foreach ($attributes as $key => $configRole) {
            if (!in_array($key, $roles)) {
                unset($attributes[$key]);
            }
        }

        if (false === $asString) {
            return array_values($attributes);
        }
        return implode(',', $attributes);
    }

    /**
     * @param array $roles
     * @return array
     */
    protected function sortRoles(array $roles = [])
    {
        return $this->sortSrcSetAttribute(null, $roles);
    }

    /**
     * @param array $roles
     * @return array
     */
    protected function sortMedia(array $roles = [])
    {
        return $this->sortSrcSetAttribute('media', $roles, false);
    }

    /**
     * @return array|null
     */
    protected function getSrcSetsConfig()
    {
        if (null === $this->imageSrcSets) {
            $this->imageSrcSets = $this->videoConfigHelper->getImageConfigHelper()->getSrcSets();
        }
        return $this->imageSrcSets;
    }

    /**
     * @param BannerModel $banner
     * @param string $role
     * @return void
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function excludeImages(BannerModel $banner, $role)
    {
        $customAttributes = $banner->getCustomAttributes();
        $images = isset($customAttributes['images']) ?
            $this->imageSerializer->unserialize($customAttributes['images']) : [];
        foreach ($images as $key => $image) {
            if ($key == $role) {
                unset($images[$key]);
            }
        }

        $customAttributes['images'] = $this->imageSerializer->serialize($images);
        $banner->setData('custom_attributes', $customAttributes);
    }

    /**
     * @param string $roles
     * @return array
     */
    protected function explodeRoles($roles)
    {
        return explode(',', $roles);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getEntityUrl($path)
    {
        return $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . $path;
    }

    /**
     * @param BannerModel $banner
     * @return array
     */
    public function setAttribute(BannerModel $banner)
    {
        $deletedVideos = $banner->getData('deleted_videos');
        $uploadedVideos = $banner->getData('uploaded_videos');
        return [
            'deleted' => $deletedVideos,
            'uploaded' => $uploadedVideos,
        ];
    }

    /***
     * @param int $bannerId
     * @param bool $forceSingle @since 3.1.0
     * @return array
     */
    public function getVideos($bannerId, $forceSingle = false)
    {
        $videos = $this->videoResource->getBannerVideos($bannerId, $forceSingle);
        $array = [];
        if (!empty($videos)) {
            foreach ($videos as $key => $video) {
                $array[$key] = [
                    'file' => $video['preview_image'],
                    'video_file' => $video['video'],
                    'video_title' => $video['video_title'],
                    'value_id' => $video['video_id'],
                    'type' => '',
                    'media_type' => \Ewave\Banner\Helper\Image\Config::BANNER_GALLERY_TYPE_VIDEO,
                    'role_code' => $this->getRoleLabel(isset($video['video_role_ids']) ? $video['video_role_ids'] : ''),
                    'allow_video_popup' => $video['allow_video_popup'],
                    'play_video_in_a_loop' => $video['play_video_in_a_loop'],
                    'play_video_after' => $video['play_video_after'],
                    'play_video_automatically_for_mobile' => $video['play_video_automatically_for_mobile'],
                    'play_video_automatically_for_desktop' => $video['play_video_automatically_for_desktop'],
                    'video_roles' => explode(',', $video['video_role_ids']),
                    'use_video_as_a_preview' => $video['use_video_as_a_preview'],
                    'video_folder' => isset($video['video_folder']) ? $video['video_folder'] : '',
                    'is_video_as_preview' => $this->isVideoFileWithoutPreviewImage($video['preview_image']),
                    'size' => $this->uploader->getFileSize($video['video']),
                ];
                $array[$key] = array_merge($array[$key], $this->prepareDimensions($video));
            }
        }

        return $array;
    }

    /**
     * @param string $video
     * @return bool
     */
    protected function isVideoFileWithoutPreviewImage($video)
    {
        $allowedExtensions = $this->videoConfigHelper->getPreviewImageAllowedExtensions(true);
        foreach ($allowedExtensions as $extension) {
            if (strpos($video, $extension) !== false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $roleId
     * @return string
     */
    public function getRoleLabel($roleId)
    {
        $roleLabel = [];
        if (!is_array($roleId)) {
            $roleId = explode(',', $roleId);
        }
        $roles = $this->videoConfigHelper->getRoles();
        foreach ($roleId as $roleCode) {
            if (empty($roleCode)) {
                continue;
            }
            $roleLabel[] = isset($roles[$roleCode]) ? $roles[$roleCode] : '';
        }

        return implode(', ', $roleLabel);
    }

    /**
     * @param array $item
     * @return array
     */
    protected function prepareDimensions($item)
    {
        if (!empty($item[VideoResource::SIZE_COLUMN])
            && strpos($item[VideoResource::SIZE_COLUMN], 'x') !== false
        ) {
            list($width, $height) = explode("x", $item[VideoResource::SIZE_COLUMN]);
            return [
                VideoExtensionAttributes::VIDEO_WIDTH_KEY => $width,
                VideoExtensionAttributes::VIDEO_HEIGHT_KEY => $height,
            ];
        }
        return [];
    }
}
