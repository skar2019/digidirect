<?php

namespace Ewave\Banner\Plugin\Magento\Banner\Model\ResourceModel;

use Ewave\Banner\Model\ResourceModel\Attributes as AttributesRM;
use Ewave\Banner\Model\Attributes;
use Magento\Banner\Model\ResourceModel\Banner as BannerResource;
use Magento\Banner\Model\Banner as BannerModel;

class Banner
{
    /**
     * @var AttributesRM
     */
    protected $attributesResourceModel;

    /**
     * @var Attributes
     */
    protected $bannerAttributes;

    /**
     * Banner constructor.
     *
     * @param Attributes $attributes
     * @param AttributesRM $attributesRM
     */
    public function __construct(
        Attributes $attributes,
        AttributesRM $attributesRM
    ) {
        $this->attributesResourceModel = $attributesRM;
        $this->bannerAttributes = $attributes;
    }

    /**
     * After load banner add target_type and target_id
     *
     * @param BannerResource $banner
     * @param \Closure $proceed
     * @param \Magento\Framework\Model\AbstractModel $bannerModel
     * @param int $value
     * @param null|string $field
     * @return BannerResource\
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundLoad(
        BannerResource $banner,
        \Closure $proceed,
        \Magento\Framework\Model\AbstractModel $bannerModel,
        $value,
        $field = null
    ) {
        $result = $proceed($bannerModel, $value, $field);
        $data = $this->getBannerAttributes($bannerModel->getId());
        $bannerModel->setData('custom_attributes', $data);
        if ($data) {
            foreach ($data as $name => $value) {
                $bannerModel->setData($name, $value);
            }
        }
        return $result;
    }

    /**
     * @since 3.1.0
     * @param int $bannerId
     * @return array
     */
    protected function getBannerAttributes($bannerId)
    {
        return $this->attributesResourceModel->getBannersAttributes($bannerId);
    }

    /**
     * @param \Magento\Banner\Model\ResourceModel\Banner $banner
     * @param \Closure $proceed
     * @param \Magento\Banner\Model\Banner $bannerModel
     * @return \Magento\Banner\Model\ResourceModel\Banner
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        BannerResource $banner,
        \Closure $proceed,
        BannerModel $bannerModel
    ) {
        $result = $proceed($bannerModel);
        $this->bannerAttributes->setAttributes($bannerModel);
        $this->attributesResourceModel->saveAttributes($bannerModel);
        return $result;
    }
}
