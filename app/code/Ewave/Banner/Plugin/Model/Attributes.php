<?php

namespace Ewave\Banner\Plugin\Model;

use Ewave\Banner\Helper\IssetTrait;
use Ewave\Banner\Model\Attributes as AttributesModel;
use Ewave\Banner\Helper\Image\Config as ImageConfig;
use Magento\Banner\Model\Banner as BannerModel;

class Attributes
{
    use IssetTrait;

    const BANNER_CODE = 'responsive_banner';

    /**
     * @var array
     */
    protected $config;

    /**
     * @var ImageConfig
     */
    protected $imageConfigHelper;

    /**
     * Attributes constructor.
     *
     * @param ImageConfig $imageConfigHelper
     * @param array $config
     */
    public function __construct(
        ImageConfig $imageConfigHelper,
        array $config = []
    ) {
        $this->imageConfigHelper = $imageConfigHelper;
        $this->config = $config;
    }

    /**
     * @param AttributesModel $attributes
     * @param \Closure $closure
     * @param BannerModel $banner
     * @return AttributesModel
     */
    public function aroundSetAttributes(
        AttributesModel $attributes,
        \Closure $closure,
        BannerModel $banner
    ) {
        $result = $closure($banner);
        $attributes = $banner->getData('attributes');
        if (!isset($attributes['alt']) || !$attributes['alt']) {
            $attributes['alt'] = $banner->getName();
            $banner->setData('attributes', $attributes);
        }
        $this->processExtensionAttributes($banner);
        return $result;
    }

    /**
     * @param BannerModel $banner
     * @return void
     */
    protected function processExtensionAttributes(BannerModel $banner)
    {
        $extensionAttributes = $this->getByKey($this->config, 'extension_attributes', []);
        if (!empty($extensionAttributes)) {
            $attributes = $banner->getData('attributes');
            if (!is_array($attributes)) {
                $attributes = [];
            }
            foreach ($extensionAttributes as $attributeCode => $object) {
                if (!isset($attributes[$attributeCode])) {
                    continue;
                }
                if (!($object instanceof AttributesModel\ExtensionAttributesInterface)) {
                    unset($attributes[$attributeCode]);
                    continue;
                }

                $object->saveAttribute($banner);
                unset($attributes[$attributeCode]);
            }
            $banner->setData('attributes', $attributes);
        }
    }

    /**
     * @param AttributesModel $attributes
     * @param BannerModel $banner
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetBannerAttributes(AttributesModel $attributes, BannerModel $banner)
    {
        $customAttributes = $banner->getCustomAttributes();
        if (!isset($customAttributes['video'])) {
            $customAttributes['video'] = [];
        }

        $customAttributes['media_query'] = $this->getMediaQuery();
        $banner->setCustomAttributes($customAttributes);
        return [$banner];
    }

    /**
     * @return array
     */
    protected function getMediaQuery()
    {
        $sets = $this->imageConfigHelper->getSrcSets();
        $mediaQuery = [];
        foreach ($sets as $set) {
            $codeValue = $this->imageConfigHelper->getValue($set, 'widget_code');
            if (self::BANNER_CODE != $codeValue) {
                continue;
            }
            $mediaQuery[] = $this->imageConfigHelper->getValue($set, 'media');
        }

        return $mediaQuery;
    }
}
