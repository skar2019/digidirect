<?php

namespace Ewave\Banner\Model\Attributes\ExtensionAttributes;

use Ewave\Banner\Model\ResourceModel\Video as VideoResource;
use Ewave\Banner\Model\Attributes\ExtensionAttributesInterface;
use Magento\Banner\Model\Banner as BannerModel;

/**
 * Class Video
 *
 * @package Ewave\Banner\Model\Attributes\ExtensionAttributes
 */
class Video implements ExtensionAttributesInterface
{
    const VIDEO_FILE_KEY = 'video_file';
    const VIDEO_WIDTH_KEY = 'video_width';
    const VIDEO_HEIGHT_KEY = 'video_height';

    /**
     * @var VideoResource
     */
    protected $videoResource;

    /**
     * @var array
     */
    protected $config;

    /**
     * Video constructor.
     *
     * @param VideoResource $video
     * @param array $config
     */
    public function __construct(
        VideoResource $video,
        array $config = []
    ) {
        $this->videoResource = $video;
        $this->config = $config;
    }

    /**
     * @param BannerModel $banner
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function saveAttribute(BannerModel $banner)
    {
        $attributes = $banner->getData('attributes');
        $video = isset($attributes['video']) ? $attributes['video']: [];
        $videoBanner = [
            'deleted' => [],
            'uploaded' => [],
        ];
        if (!empty($video['deleted'])) {
            foreach ($video['deleted'] as $videoToDelete) {
                $videoBanner['deleted'][] = $videoToDelete;
            }
        }

        if (!empty($video['uploaded'])) {
            foreach ($video['uploaded'] as $key => $item) {
                foreach ($this->config as $fieldCode => $objectCode) {
                    if (!empty($objectCode)) {
                        $value = isset($item[$objectCode]) ? $item[$objectCode] : null;
                    } else {
                        $value = isset($item[$fieldCode]) ? $item[$fieldCode] : null;
                    }
                    $videoBanner['uploaded'][$key][$fieldCode] = $value;
                    if ($fieldCode == VideoResource::SIZE_COLUMN) {
                        $videoBanner['uploaded'][$key][$fieldCode] = $this->prepareSize($item);
                    }
                }
            }
        }

        $banner->setVideo($videoBanner);
        $this->videoResource->saveVideo($banner);
    }

    /**
     * @param array $uploadedItem
     * @return null|string
     */
    protected function prepareSize($uploadedItem)
    {
        if (!empty($uploadedItem) && $uploadedItem[self::VIDEO_WIDTH_KEY] && $uploadedItem[self::VIDEO_HEIGHT_KEY]) {
            return $uploadedItem[self::VIDEO_WIDTH_KEY] . 'x' . $uploadedItem[self::VIDEO_HEIGHT_KEY];
        }
        return null;
    }
}
