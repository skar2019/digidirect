<?php

namespace Ewave\Utilities\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class WysiwygSettings
 * @package Ewave\Utilities\Helper
 */
class WysiwygAllowedTypeSettings extends AbstractHelper
{
    const XML_PATH_ALLOWED_FILETYPES = 'ewave_utilities_config/wysiwyg/allowed_filetypes';
    const XML_PATH_ALLOWED_TAGS = 'ewave_utilities_config/wysiwyg/allowed_tags';
    const XML_PATH_WRAP_IN_PARENT_TAG = 'ewave_utilities_config/wysiwyg/wrap_in_parent_tag';
    const XML_PATH_ALLOWED_CHILDS  = 'ewave_utilities_config/wysiwyg/allowed_childs';

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * Settings constructor.
     * @param Context $context
     * @param Json    $serializer
     */
    public function __construct(
        Context $context,
        Json $serializer
    ) {
        parent::__construct($context);
        $this->serializer = $serializer;
    }

    /**
     * @return array
     */
    public function getAdditionalFiletypes()
    {
        $filetypes = [];
        $allowedTypes = $this->getAllowedFiletypes();
        if ($allowedTypes && $settings = $this->serializer->unserialize($allowedTypes)) {
            foreach ($settings as $setting) {
                $filetypes[] = $setting['extension'];
            }
        }

        return $filetypes;
    }

    /**
     * @param array $pathInfo
     * @return bool
     */
    public function isAdditionalFiletype(array $pathInfo)
    {
        return array_key_exists('extension', $pathInfo)
            && in_array($pathInfo['extension'], $this->getAdditionalFiletypes(), true);
    }

    /**
     * @return mixed
     */
    protected function getAllowedFiletypes()
    {
        return $this->scopeConfig->getValue(static::XML_PATH_ALLOWED_FILETYPES);
    }

    /**
     * Return allowed tags string
     * @return string
     */
    public function getAllowedTags()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ALLOWED_TAGS);
    }

    /**
     * Return is parent tag should be wrapped
     * @return bool
     */
    public function hasWrapInParentTag()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_WRAP_IN_PARENT_TAG);
    }

    /**
     * Return allowed childs string
     * @return string
     */
    public function getAllowedChilds()
    {

        return $this->scopeConfig->getValue(self::XML_PATH_ALLOWED_CHILDS);
    }
}
