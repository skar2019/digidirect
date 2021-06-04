<?php

namespace Ewave\Banner\Model\Attributes;

use Ewave\Banner\Model\Attributes\TargetType\Config as TargetTypeConfig;
use Ewave\Banner\Model\Attributes\TargetType\TargetTypeInterface;
use Magento\Banner\Model\Banner as BannerModel;

class TargetLink implements AttributesInterface
{
    const TARGET_TYPE_CODE = 'target_type';
    const TARGET_ID = 'target_id';

    /**
     * @var Config
     */
    protected $targetTypeConfig;

    /**
     * TargetLink constructor.
     *
     * @param TargetTypeConfig $config
     */
    public function __construct(TargetTypeConfig $config)
    {
        $this->targetTypeConfig = $config;
    }

    /**
     * @param BannerModel $banner
     * @return string
     */
    public function getFrontendAttribute(BannerModel $banner)
    {
        $targetModel = $this->_getTargetTypeModel($banner);
        return $targetModel ? $targetModel->getUrl($banner->getData(self::TARGET_ID)) : null;
    }

    /**
     * @param BannerModel $banner
     * @return mixed|null
     */
    public function setAttribute(BannerModel $banner)
    {
        $targetTypeObject = $this->_getTargetTypeModel($banner);
        return $targetTypeObject ? $targetTypeObject->getBackendAttributeForSave($banner) : null;
    }

    /**
     * @param BannerModel $banner
     * @return TargetTypeInterface|null
     */
    protected function _getTargetTypeModel(BannerModel $banner)
    {
        $targetTypeObject = $this->targetTypeConfig->getTargetTypeObject($banner->getData(self::TARGET_TYPE_CODE));
        if (!($targetTypeObject instanceof TargetTypeInterface)) {
            return null;
        }

        return $targetTypeObject;
    }

    /**
     * @param BannerModel $banner
     * @param null $currentAttributeCode
     * @return mixed|null
     */
    public function getBackendAttributeForEdit(BannerModel $banner, $currentAttributeCode = null)
    {
        $targetModel = $this->_getTargetTypeModel($banner);
        return $targetModel ? $targetModel->getBackendAttributeForEdit($banner, $currentAttributeCode) : null;
    }

    /**
     * @param string $targetTypeCode
     * @param string $targetValue
     * @return string|null
     */
    public function getTargetValue($targetTypeCode, $targetValue)
    {
        $targetModel = $this->targetTypeConfig->getTargetTypeObject($targetTypeCode);
        if ($targetModel instanceof TargetTypeInterface) {
            return $targetModel->getValue($targetValue);
        }
        return null;
    }

    /**
     * @return Config|TargetTypeConfig
     */
    public function getConfig()
    {
        return $this->targetTypeConfig;
    }
}
