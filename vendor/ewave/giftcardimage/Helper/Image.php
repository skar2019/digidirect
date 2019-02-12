<?php
namespace Ewave\GiftCardImage\Helper;

use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;

/**
 * Class Image
 * @package Ewave\GiftCardImage\Helper
 */
class Image extends \Ewave\Utilities\Helper\Image
{
    const IMAGE_ID = 'image';
    const IMAGE_INFO = 'image_info';

    const IMAGES_INFO = [
        self::IMAGE_ID => self::IMAGE_INFO
    ];

    /**
     * @var \Ewave\GiftCardImage\Model\Media\Config
     */
    protected $mediaConfig;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\Framework\File\Mime
     */
    protected $mime;

    /**
     * Image constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Ewave\Utilities\Model\ImageFactory $imageFactory
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\View\ConfigInterface $viewConfig
     * @param \Ewave\Utilities\Model\Media\Config $mediaConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\File\Mime $mime
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Ewave\Utilities\Model\ImageFactory $imageFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Ewave\Utilities\Model\Media\Config $mediaConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Mime $mime = null
    ) {
        $this->mediaConfig = $mediaConfig;
        $this->filesystem = $filesystem;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->mime = $mime ?: ObjectManager::getInstance()->get(\Magento\Framework\File\Mime::class);
        parent::__construct($context, $imageFactory, $assetRepo, $viewConfig);
    }

    /**
     * Initialize Helper to work with Image
     * @param GiftCardImageInterface|\Magento\Catalog\Model\Product|DataObject $giftCardImage
     * @param string $imageId
     * @param array $attributes
     * @return $this
     */
    public function init($giftCardImage, $imageId, $attributes = [])
    {
        $imageType = $attributes['type'] ?? 'ewave_giftcard_image';
        parent::init($giftCardImage, $imageType, $attributes);
        $this->setImageFile($this->getGiftCardImage($giftCardImage, $imageId));
        return $this;
    }

    /**
     * Get image url from object
     * @param DataObject $giftCardImage
     * @param string $key
     * @return string
     */
    public function getGiftCardImageUrl($giftCardImage = null, $key = self::IMAGE_ID)
    {
        return $this->mediaConfig->getBaseMediaUrl() . $this->getGiftCardImage($giftCardImage, $key);
    }

    /**
     * Get image from object
     * @param DataObject|null $giftCardImage
     * @param string $key
     * @return string
     */
    public function getGiftCardImage(DataObject $giftCardImage = null, $key = self::IMAGE_ID)
    {
        if (null === $giftCardImage) {
            $giftCardImage = $this->getEntity();
        }
        return $giftCardImage->getData($key);
    }

    /**
     * Get image information from object
     * @param DataObject $option
     * @return DataObject
     */
    public function addImagesInfo(DataObject $option)
    {
        foreach (array_keys(self::IMAGES_INFO) as $key) {
            $this->addImageInfo($option, $key);
        }

        return $option;
    }

    /**
     * Get image information from object
     * @param DataObject|array $giftCardImage
     * @param bool $delete
     * @return \string[]
     */
    public function getGiftCardImageInfo($giftCardImage, $delete = true)
    {
        return $this->getImageInfo($giftCardImage, self::IMAGE_INFO, $delete);
    }

    /**
     * Get image information from object
     * @param GiftCardImageInterface|DataObject|array $giftCardImage
     * @param string $key
     * @param bool $delete
     * @return \string[]
     */
    public function getImageInfo($giftCardImage, $key = self::IMAGE_INFO, $delete = true)
    {
        if ($giftCardImage instanceof DataObject) {
            $imageInfo = $giftCardImage->getData($key);
            if ($delete) {
                $giftCardImage->unsetData($key);
            }
        } elseif (is_array($giftCardImage) && isset($option[$key])) {
            $imageInfo = $option[$key];
            if ($delete) {
                unset($option[$key]);
            }
        }

        if (!empty($imageInfo)) {
            return current($imageInfo);
        }

        return [];
    }

    /**
     * Get image information from object
     * @param DataObject|array|string $giftCardImage
     * @param string $key
     * @return bool
     */
    public function deleteOldImage($giftCardImage, $key = self::IMAGE_ID)
    {
        if ($giftCardImage instanceof DataObject) {
            $images = $giftCardImage->getData($key);
        } elseif (is_array($giftCardImage)) {
            $images = !empty($option[$key]) ? $option[$key] : $option;
        } else {
            $images = strval($giftCardImage);
        }

        if (!empty($images)) {
            if (!is_array($images)) {
                $images = [$images];
            }

            foreach ($images as $image) {
                if (!$image) {
                    continue;
                }
                $fileName = $this->mediaConfig->getBaseMediaPath() . $image;
                $this->mediaDirectory->delete($fileName);
            }

            return true;
        }

        return false;
    }

    /**
     * Get image url from object
     * @param DataObject $giftCardImage
     * @param string $key
     * @return string
     */
    public function getOriginalImageUrl($giftCardImage = null, $key = self::IMAGE_ID)
    {
        return $this->mediaConfig->getBaseMediaUrl() . $this->getGiftCardImage($giftCardImage, $key);
    }

    /**
     * Get image information from object
     * @param DataObject $giftCardImage
     * @param string $key
     * @return DataObject
     */
    protected function addImageInfo(DataObject $giftCardImage, $key = self::IMAGE_ID)
    {
        if ($image = $giftCardImage->getData($key)) {
            $fileName = $this->mediaConfig->getBaseMediaPath() . $image;
        }

        $infoKey = self::IMAGES_INFO[$key];
        if (isset($fileName) && $this->mediaDirectory->isExist($fileName)) {
            $stat = $this->mediaDirectory->stat($fileName);
            $imageInfo = [
                [
                    'url'    => $this->getOriginalImageUrl($giftCardImage, $key),
                    'file'   => $image,
                    'size'   => is_array($stat) ? $stat['size'] : 0,
                    'exists' => true,
                    'type' => $this->mime->getMimeType($this->mediaDirectory->getAbsolutePath($fileName)),
                ]
            ];
            $giftCardImage->setData($infoKey, $imageInfo);
        } else {
            $giftCardImage->unsetData($infoKey);
        }

        return $giftCardImage;
    }
}
