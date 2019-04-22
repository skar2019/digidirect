<?php

namespace Ewave\Banner\Block\Adminhtml\Banner\Helper\Form\Gallery;

use Ewave\Banner\Component\Json;
use Ewave\Banner\Helper\IssetTrait;
use Magento\Backend\Block\DataProviders\ImageUploadConfig as ImageUploadConfigDataProvider;
use Magento\Framework\App\ObjectManager;

class Content extends \Magento\Backend\Block\Widget
{
    use IssetTrait;

    /**
     * Template file
     *
     * @var string
     */
    protected $_template = 'Ewave_Banner::gallery.phtml';

    /**
     * @var Json
     */
    protected $jsonEncoder;

    /**
     * @var \Ewave\Banner\Helper\Image\Config
     */
    protected $imageConfig;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var ImageUploadConfigDataProvider
     */
    protected $imageUploadConfigDataProvider;

    /**
     * Content constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Json $jsonEncoder
     * @param \Ewave\Banner\Helper\Image\Config $imageConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Json $jsonEncoder,
        \Ewave\Banner\Helper\Image\Config $imageConfig,
        array $data = [],
        ImageUploadConfigDataProvider $imageUploadConfigDataProvider = null
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->imageConfig = $imageConfig;
        $this->assetRepo = $context->getAssetRepository();
        parent::__construct($context, $data);
        $this->imageUploadConfigDataProvider = $imageUploadConfigDataProvider
            ?: ObjectManager::getInstance()->get(ImageUploadConfigDataProvider::class);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'uploader', \Magento\Backend\Block\Media\Uploader::class,
            ['image_upload_config_data' => $this->imageUploadConfigDataProvider]
        );

        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->addSessionParam()->getUrl('ewave_banner/gallery/upload')
        )->setFileField(
            'image'
        )->setFilters(
            [
                'images' => [
                    'label' => __('Images (.gif, .jpg, .png)'),
                    'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                ],
            ]
        );

        return parent::_prepareLayout();
    }

    /**
     * Retrieve uploader block
     *
     * @return Uploader
     */
    public function getUploader()
    {
        return $this->getChildBlock('uploader');
    }

    /**
     * Retrieve uploader block html
     *
     * @return string
     */
    public function getUploaderHtml()
    {
        return $this->getChildHtml('uploader');
    }

    /**
     * Get unique js object name
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * Get buttons html
     *
     * @return string
     */
    public function getAddImagesButton()
    {
        return $this->getButtonHtml(
            __('Add New Images'),
            $this->getJsObjectName() . '.showUploader()',
            'add',
            $this->getHtmlId() . '_add_images_button'
        );
    }

    /**
     * @param string $path
     * @param bool $videoAsPreview
     * @return string
     */
    protected function getEntityUrl($path, $videoAsPreview = false)
    {
        return ($path && !$videoAsPreview) ? $this->_urlBuilder->getBaseUrl(
            ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
        ) . $path : $this->assetRepo->getUrl('Ewave_Banner::images/placeholder.jpg');
    }

    /**
     * Get json images array
     *
     * @return string
     */
    public function getImagesJson()
    {
        $imageInfo = $this->getElement()->getDataObject()->getData('images_banner');

        if (!empty($imageInfo)) {
            $mediaAttributes = $this->getMediaAttributes();
            foreach ($imageInfo as $key => $image) {
                $fileToEntity = isset($image['file']) ? $image['file'] : null;
                $videoAsPreview = isset($image['is_video_as_preview']) ? $image['is_video_as_preview'] : null;
                $url = $this->getEntityUrl($fileToEntity, $videoAsPreview);
                $videoFile = $this->getByKey($image, 'video_file');
                $videoUrl = $this->getEntityUrl($videoFile);
                $roleCode = $this->getByKey($image, 'role_code', '');
                if ($videoFile) {
                    $imageInfo[$key]['video_url'] = $videoUrl;
                }
                $imageInfo[$key]['role_code'] = $this->getRoleLabel($image, $mediaAttributes);
                $imageInfo[$key]['role'] = $roleCode;
                $imageInfo[$key]['url'] = $url;
            }

            return $this->jsonEncoder->encode($imageInfo);
        }

        return '[]';
    }

    /**
     * @param [] $image
     * @param [] $mediaAttributes
     * @return \Magento\Framework\Phrase|string
     */
    protected function getRoleLabel($image, $mediaAttributes)
    {
        $roleCode = $this->getByKey($image, 'role_code', '');
        $mediaType = $this->getByKey($image, 'media_type');
        if ($mediaType == \Ewave\Banner\Helper\Image\Config::BANNER_GALLERY_TYPE_VIDEO) {
            return $roleCode;
        }

        return !empty($mediaAttributes[$roleCode]) ? $mediaAttributes[$roleCode] : __('No Role');
    }

    /**
     * Get image types data
     *
     * @return []
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getImageTypes()
    {
        $imageTypes = [];
        foreach ($this->getMediaAttributes() as $code => $attribute) {
            $imageTypes[$code] = [
                'code' => $code,
                'value' => $this->_getImage($code),
                'label' => $code,
                'name' => $code,
                'real_label' => $attribute,
            ];
        }
        return $imageTypes;
    }

    /**
     * @param string $code
     * @return string
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _getImage($code)
    {
        $images = $this->getElement()->getDataObject()->getImagesBanner();
        if (!is_array($images)) {
            return '';
        }
        foreach ($images as $image) {
            if ($image['type'] == $code) {
                return $value = $image['file'];
            }
        }
        return '';
    }

    /**
     * Get Media Attributes
     *
     * @return []
     */
    public function getMediaAttributes()
    {
        return $this->imageConfig->getAllSrcSets();
    }

    /**
     * @return array
     */
    public function getMediaAttributesGroupedByCode()
    {
        return $this->imageConfig->getAllSrcSetsGroupByCode();
    }

    /**
     * Get types
     *
     * @return string
     */
    public function getImageTypesJson()
    {
        return $this->jsonEncoder->encode($this->getImageTypes());
    }
}
