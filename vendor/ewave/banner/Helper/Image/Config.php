<?php

namespace Ewave\Banner\Helper\Image;

use Ewave\Banner\Component\Json;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context as HelperContext;
use Ewave\Banner\Model\Image\Uploader as ImageUploader;

class Config extends AbstractHelper
{
    const SEP = '/';
    const BANNER_CONFIG_PATH = 'ewave_banner';
    const IMAGES_UPLOADED_ELEMENT_VAR_NAME = 'banner_images';
    const IMAGES_UPLOADED_IMAGES_VAR_NAME = 'images';
    const PROMOTION_BANNER_IMAGE_SETTINGS = 'ewave_banner/image_uploading/';
    const BANNER_GALLERY_TYPE_IMAGE = 'image';
    const BANNER_GALLERY_TYPE_VIDEO = 'external-video';
    const BANNER_DEVELOPER_PATH = self::BANNER_CONFIG_PATH . self::SEP . 'developer';

    /**
     * @var \Magento\Framework\Config\View
     */
    protected $viewConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Json
     */
    protected $jsonComponent;

    /**
     * @var null|[]
     */
    protected $srcSetsConfigurationArray = null;

    /**
     * Config constructor.
     *
     * @param HelperContext $context
     * @param \Ewave\Banner\Model\Config\View $view
     * @param StoreManagerInterface $storeManager
     * @param Json $jsonComponent
     */
    public function __construct(
        HelperContext $context,
        \Ewave\Banner\Model\Config\View $view,
        StoreManagerInterface $storeManager,
        Json $jsonComponent
    ) {
        parent::__construct($context);
        $this->viewConfig = $view;
        $this->storeManager = $storeManager;
        $this->jsonComponent = $jsonComponent;
    }

    /**
     * Get all src sets names/codes
     *
     * @return []
     */
    public function getSrcSets()
    {
        $srcCodesConfig = $this->_getModuleVars();
        if (!empty($this->getSrcSetsConfigurationAdmin())) {
            foreach ($this->getSrcSetsConfigurationAdmin() as $code => $value) {
                if (!isset($srcCodesConfig[$code])) {
                    $srcCodesConfig[$code] = $value;
                }
            }
        }
        return array_keys($srcCodesConfig);
    }

    /**
     * Get image height and width
     *
     * @param string $setCode
     * @return []
     */
    public function getImageSizeBySet($setCode)
    {
        return [
            'height' => $this->getImageHeight($setCode),
            'width' => $this->getImageWidth($setCode),
        ];
    }

    /**
     * @param string $setCode
     * @return string
     */
    public function getImageHeight($setCode)
    {
        $configurationValue = $this->getConfigurationValueAdmin($setCode, 'height');
        if ($configurationValue) {
            return $configurationValue;
        }
        return $this->_getImageConfigValue($setCode, 'height');
    }

    /**
     * @param string $setCode
     * @return string
     */
    public function getImageWidth($setCode)
    {
        $configurationValue = $this->getConfigurationValueAdmin($setCode, 'width');
        if ($configurationValue) {
            return $configurationValue;
        }
        return $this->_getImageConfigValue($setCode, 'width');
    }

    /**
     * Since 3.0.0 admin configuration is a priority
     *
     * @param string $setCode
     * @param string $value
     * @return string
     */
    protected function _getImageConfigValue($setCode, $value)
    {
        $configurationValue = $this->getConfigurationValueAdmin($setCode, $value);
        if ($configurationValue) {
            return $configurationValue;
        }
        $viewValue = $this->_getModuleVarValue($setCode, $value);
        return $viewValue ?: null;
    }

    /**
     * @return string
     */
    public function getDefaultSrcSetName()
    {
        return $this->scopeConfig->getValue(self::BANNER_CONFIG_PATH . '/srcset/default');
    }

    /**
     * @return array
     */
    public function getDefaultImageSize()
    {
        return [
            'width' => $this->scopeConfig->getValue(
                self::BANNER_CONFIG_PATH . '/' . $this->getDefaultSrcSetName() . '/width'
            ),
            'height' => $this->scopeConfig->getValue(
                self::BANNER_CONFIG_PATH . '/' . $this->getDefaultSrcSetName() . '/height'
            ),
        ];
    }

    /**
     * @return array
     */
    public function getAllSrcSets()
    {
        $sets = [];
        foreach ($this->getSrcSets() as $srcSetCode) {
            $sets[$srcSetCode] = $this->_getModuleVarValue($srcSetCode, 'srcset_title');
        }
        foreach ($this->getSrcSetsStoresConfiguration() as $code => $configuration) {
            $sets[$code] = $this->getArrayKey($configuration, 'srcset_title');
        }
        return $sets;
    }

    /**
     * @return array
     */
    public function getAllSrcSetsGroupByCode()
    {
        $sets = [];
        foreach ($this->getSrcSets() as $srcSetCode) {
            $codeValue = $this->_getModuleVarValue($srcSetCode, 'widget_code');
            $sets[$codeValue][$srcSetCode] = $this->getRoleLabel($srcSetCode);
        }
        return $sets;
    }

    /**
     * @param string $roleCode
     * @param bool $resolution
     * @return array|string
     */
    public function getRoleLabel($roleCode, $resolution = true)
    {
        $label = $this->_getModuleVarValue($roleCode, 'srcset_title');
        if ($resolution) {
            $label .= ' ' . $this->getImageWidth($roleCode) . 'x'
                . $this->getImageHeight($roleCode);
        }
        return $label;
    }

    /**
     * @return array
     */
    protected function _getModuleVars()
    {
        return $this->viewConfig->getVars('Ewave_Banner');
    }

    /**
     * @param string $path
     * @param string|null $key
     * @return array|string
     */
    protected function _getModuleVarValue($path, $key = null)
    {
        $array = $this->_getModuleVars();
        if (null === $path) {
            return isset($array[$path]) ? $array[$path] : [];
        }

        return isset($array[$path][$key]) ? $array[$path][$key] : null;
    }

    /**
     * Filesystem directory path of temporary product images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseTmpMediaPath()
    {
        return 'tmp/' . $this->getBaseMediaPathAddition();
    }

    /**
     * Filesystem directory path of product images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseMediaPathAddition()
    {
        return ImageUploader::ORIGINAL_UPLOADED_FILE_TMP_PATH;
    }

    /**
     * Concatenation tmp path with file
     *
     * @param string $file
     * @return string
     */
    public function getTmpMediaUrl($file)
    {
        return $this->getBaseTmpMediaUrl() . '/' . $this->_prepareFile($file);
    }

    /**
     * Get base tmp media url
     *
     * @return string
     */
    public function getBaseTmpMediaUrl()
    {
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl . 'tmp/' . $this->getBaseMediaUrlAddition();
    }

    /**
     * Replace slashes
     *
     * @param string $file
     * @return string
     */
    protected function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * @return string
     */
    public function getBaseMediaUrlAddition()
    {
        return ImageUploader::ORIGINAL_UPLOADED_FILE_TMP_PATH;
    }

    /**
     * @return mixed
     */
    public function getAllowedImagesMimeTypes()
    {
        return $this->scopeConfig->getValue(
            self::PROMOTION_BANNER_IMAGE_SETTINGS . 'image_mime_type',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Since 3.0.0 admin configuration is a priority
     *
     * @param string $code
     * @return array|string
     */
    public function getSrcSetValueByCode($code)
    {
        $configurationValue = $this->getConfigurationValueAdmin($code, 'srcset_value');
        if ($configurationValue) {
            return $configurationValue;
        }
        return $this->_getModuleVarValue($code, 'srcset_value');
    }

    /**
     * Since 3.0.0 admin configuration is a priority
     *
     * @param string $code
     * @return array|string
     */
    public function getSrcSetMedia($code)
    {
        $configurationValue = $this->getConfigurationValueAdmin($code, 'media');
        if ($configurationValue) {
            return $configurationValue;
        }
        return $this->_getModuleVarValue($code, 'media');
    }

    /**
     * Since 3.0.0 admin configuration is a priority
     *
     * @param string $code
     * @param string $attribute
     * @return array|string
     */
    public function getValue($code, $attribute)
    {
        $configurationValue = $this->getConfigurationValueAdmin($code, $attribute);
        if ($configurationValue) {
            return $configurationValue;
        }
        return $this->_getModuleVarValue($code, $attribute);
    }

    /**
     * @return array
     */
    public function getAllowedExtensions()
    {
        return explode(',', $this->scopeConfig->getValue(self::PROMOTION_BANNER_IMAGE_SETTINGS . 'image_type'));
    }

    /**
     * @return array
     */
    protected function getSrcSetsStoresConfiguration()
    {
        return $this->getSrcSetsConfigurationAdmin();
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getSrcSetsConfigurationAdmin()
    {
        if (null === $this->srcSetsConfigurationArray) {
            $configuration = $this->scopeConfig->getValue(
                static::BANNER_DEVELOPER_PATH . static::SEP . 'banner_configuration'
            );
            try {
                $configurationArray = $this->jsonComponent->decode($configuration);
                foreach ($configurationArray as $hash => $itemConfiguration) {
                    if ($this->hasEmpty($itemConfiguration)) {
                        continue;
                    }
                    $code = $this->getArrayKey($itemConfiguration, 'code_column');
                    foreach ($itemConfiguration as $key => $value) {
                        $this->srcSetsConfigurationArray[$code][$this->replaceColumn($key)] = $value;
                    }
                }
            } catch (\Throwable $exception) {
                $this->srcSetsConfigurationArray = [];
            } finally {
                if (null === $this->srcSetsConfigurationArray) {
                    $this->srcSetsConfigurationArray = [];
                }
            }
        }

        return $this->srcSetsConfigurationArray;
    }

    /**
     * @param string $string
     * @return mixed
     */
    private function replaceColumn($string)
    {
        return \str_replace('_column', '', $string);
    }

    /**
     * @param array $array
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    private function getArrayKey(array $array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    /**
     * @param array $array
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function hasEmpty(array $array)
    {
        foreach ($array as $key => $value) {
            if (empty($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $srcSetCode
     * @param string $valueToFetch
     * @return null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function getConfigurationValueAdmin($srcSetCode, $valueToFetch)
    {
        return null;
    }
}
