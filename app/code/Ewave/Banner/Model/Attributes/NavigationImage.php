<?php

namespace Ewave\Banner\Model\Attributes;

use Ewave\Banner\Model\Image\Uploader;
use Ewave\Banner\Model\Upload\ImageProcessor;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Rss\UrlBuilderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Magento\Banner\Model\Banner as BannerModel;

class NavigationImage implements AttributesInterface
{
    const FOLDER = 'navigation_image';
    const ATTRIBUTE_CODE = 'navigation_image';

    /**
     * @var Uploader
     */
    protected $uploader;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var UrlBuilderInterface
     */
    protected $urlBuilder;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ImageProcessor
     */
    protected $imageProcessor;

    /**
     * @var null|bool|string
     */
    protected static $navigationImage;

    /**
     * NavigationImage constructor.
     * @param ImageProcessor $imageProcessor
     * @param Uploader $uploader
     * @param Filesystem $filesystem
     * @param UrlInterface $urlBuilder
     * @param RequestInterface $request
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        ImageProcessor $imageProcessor,
        Uploader $uploader,
        Filesystem $filesystem,
        UrlInterface $urlBuilder,
        RequestInterface $request
    )
    {
        $this->uploader = $uploader;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->imageProcessor = $imageProcessor;
    }

    /**
     * @param BannerModel $banner
     * @return null|string
     */
    public function getFrontendAttribute(BannerModel $banner)
    {
        $customAttributes = $banner->getCustomAttributes();
        if (isset($customAttributes['navigation_title_type'])
            && $customAttributes['navigation_title_type'] == NavigationTitle::NAVIGATION_TYPE_TITLE_IMAGE
        ) {
            $customAttributes['image'] = true;
            $banner->setData('custom_attributes', $customAttributes);
            return !empty($customAttributes['navigation_image']) ?
                $this->imageProcessor->getWebUrl(
                    $customAttributes['navigation_image'],
                    self::ATTRIBUTE_CODE
                )
                : null;
        }
        $customAttributes['image'] = false;
        $banner->setData('custom_attributes', $customAttributes);
        return null;
    }

    /**
     * @param BannerModel $banner
     * @return bool|null|string
     * @throws LocalizedException
     */
    public function setAttribute(BannerModel $banner)
    {
        if (self::$navigationImage !== true) {
            self::$navigationImage = $this->request->getParam(NavigationImage::ATTRIBUTE_CODE);
        }
        try {
            $navigationImage = $banner->getData(self::ATTRIBUTE_CODE);
            $navigationImage = is_array($navigationImage) ? current($navigationImage) : $navigationImage;
            $uploadedName = null;

            $banner->setImagesFields([NavigationImage::ATTRIBUTE_CODE]);

            //if save new image
            if (self::$navigationImage !== true && !empty($navigationImage['name'])) {

                $customAttributes = $banner->getCustomAttributes();
                $navigationImageName = $customAttributes[self::ATTRIBUTE_CODE] ?? null;
                $banner->setData('old_' . self::ATTRIBUTE_CODE, $navigationImageName);
                $banner->setData(self::ATTRIBUTE_CODE, basename($navigationImage['name']));
                $banner->setData('navigation_image_data', self::$navigationImage);
                $result = $this->imageProcessor->saveImages($banner);
                $uploadedName = $result[NavigationImage::ATTRIBUTE_CODE] ?? '';

            } else if (!empty($navigationImage['file'])) { // if save without change image

                $uploadedName = $navigationImage['file'];
                $banner->setData(self::ATTRIBUTE_CODE, $uploadedName);

            } else if (empty(self::$navigationImage)) { // if remove exists image

                $banner->setData(self::ATTRIBUTE_CODE, null);
                $this->imageProcessor->saveImages($banner);
            }
            if (self::$navigationImage !== true) {
                self::$navigationImage = true;
                return $uploadedName;
            }
            return $this->getImage($banner);
        } catch (\Exception $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * @param BannerModel $banner
     * @return string|null
     */
    protected function getImage(BannerModel $banner)
    {
        $data = $banner->getData(self::ATTRIBUTE_CODE);
        if (is_array($data)) {
            if (isset($data['delete'])) {
                return null;
            }

            if (isset($data['value'])) {
                return $data['value'];
            }
        }
        return $banner->getData(self::ATTRIBUTE_CODE);
    }
}
