<?php

namespace Ewave\Banner\Model\Attributes;

use Ewave\Banner\Model\Image\ImageSerializer;
use Magento\Framework\UrlInterface;
use Ewave\Banner\Helper\Image\Config as ImageConfig;
use Magento\Banner\Model\Banner as BannerModel;

class Image implements AttributesInterface
{
    const ATTRIBUTE_CODE = 'images';

    /**
     * @var ImageConfig
     */
    protected $config;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Ewave\Banner\Model\Image\Uploader\Proxy
     */
    protected $uploader;

    /**
     * @var ImageSerializer
     */
    protected $imageSerializer;

    /**
     * Image constructor.
     *
     * @param UrlInterface $url
     * @param ImageConfig $config
     * @param \Ewave\Banner\Model\Image\Uploader\Proxy $uploader
     */
    public function __construct(
        UrlInterface $url,
        ImageConfig $config,
        \Ewave\Banner\Model\Image\Uploader\Proxy $uploader,
        ImageSerializer $imageSerializer
    ) {
        $this->imageSerializer = $imageSerializer;
        $this->config = $config;
        $this->urlBuilder = $url;
        $this->uploader = $uploader;
    }

    /**
     * @param [] $images
     * @return []
     */
    public function getBannerImages(array $images = [])
    {
        $bannerImages = [];

        $imagesRoles = $this->config->getSrcSets();
        if (!empty($imagesRoles)) {
            foreach ($imagesRoles as $imagesRole) {
                $imageToProcess = isset($images[$imagesRole]) ? $images[$imagesRole] : null;
                if (!$imageToProcess) {
                    continue;
                }
                $bannerImages[] = [
                    'src_set_code' => $imagesRole,
                    'src_set_value' => $this->config->getSrcSetValueByCode($imagesRole),
                    'src_set_image' => $this->getImageUrl($imageToProcess),
                    'media' => $this->config->getSrcSetMedia($imagesRole),
                ];
                unset($images[$imagesRole]);
            }
        }

        if (!empty($images)) {
            $images = [end($images)];
        }
        foreach ($images as $imageSet => $imagePath) {
            $bannerImages[] = [
                'src_set_code' => $imageSet,
                'src_set_value' => $this->config->getSrcSetValueByCode($imageSet),
                'src_set_image' => $this->getImageUrl($imagePath),
                'media' => $this->config->getSrcSetMedia($imageSet),
            ];
            break;
        }

        return $bannerImages;
    }

    /**
     * Get image url
     *
     * @param string $imagePath
     * @return string
     */
    public function getImageUrl($imagePath)
    {
        return $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . $imagePath;
    }

    /**
     * Process images.
     * 1)If newly uploaded
     * 2)updated
     * 3)deleted
     *
     * @param BannerModel $banner
     * @return string
     */
    public function setAttribute(BannerModel $banner)
    {
        $uploadedImages = $banner->getData('uploaded_images');
        $deletedImages = $banner->getData('deleted_images');
        $images = $banner->getData('images');
        $imagesAsArray = [];
        if ($images) {
            $imagesAsArray = $this->imageSerializer->unserialize($images);
        }

        $imagesAsArray = $this->_processDeletedImages($deletedImages, $imagesAsArray);
        $imagesAsArray = $this->_processUploadedImages($uploadedImages, $imagesAsArray);

        $imagesSave = $this->imageSerializer->serialize($imagesAsArray);
        return $imagesSave;
    }

    /**
     * @param [] $deletedImages
     * @param [] $imagesAsArray
     * @return []
     */
    protected function _processDeletedImages($deletedImages, $imagesAsArray)
    {
        if (!empty($deletedImages)) {
            foreach ($deletedImages as $code) {
                if (isset($imagesAsArray[$code])) {
                    unset($imagesAsArray[$code]);
                }
            }
        }
        return $imagesAsArray;
    }

    /**
     * @param [] $uploadedImages
     * @param [] $imagesAsArray
     * @return []
     */
    protected function _processUploadedImages($uploadedImages, $imagesAsArray)
    {
        if (!empty($uploadedImages)) {
            $imagesAsArray = array_merge($imagesAsArray, $uploadedImages);
        }
        return $imagesAsArray;
    }

    /**
     * @param BannerModel $banner
     * @return []
     */
    public function getAttributeValue(BannerModel $banner)
    {
        return $this->imageSerializer->unserialize($banner->getData('images'));
    }

    /**
     * @param BannerModel $banner
     * @return array
     */
    public function getFrontendAttribute(BannerModel $banner)
    {
        $customAttributes = $banner->getCustomAttributes();
        $images = isset($customAttributes[self::ATTRIBUTE_CODE]) ? $customAttributes[self::ATTRIBUTE_CODE] : [];
        if (!empty($images)) {
            return $this->getBannerImages($this->imageSerializer->unserialize($images));
        }
        return $images;
    }
}
