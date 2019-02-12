<?php

namespace Ewave\Banner\Plugin\Model\Banner;

use Ewave\Banner\Model\Attributes\NavigationImage;
use Ewave\Banner\Model\Upload\ImageProcessor;
use Magento\Banner\Model\Banner\DataProvider as BannerDataProvider;
use Ewave\Banner\Model\ResourceModel\Attributes as AttributesResourceModel;
use Ewave\Banner\Model\Attributes\TargetLink;

class DataProvider
{
    /**
     * @var AttributesResourceModel 
     */
    protected $attributesResourceModel;

    /**
     * @var ImageProcessor
     */
    protected $imageProcessor;

    /**
     * @var \Ewave\Banner\Model\Attributes\TargetLink
     */
    protected $targetTypeAttribute;

    /**
     * DataProvider constructor.
     * @param AttributesResourceModel $attributesResourceModel
     * @param ImageProcessor $imageProcessor
     * @param TargetLink $targetType
     */
    public function __construct(
        AttributesResourceModel $attributesResourceModel,
        ImageProcessor $imageProcessor,
        TargetLink $targetType
    ) {
        $this->attributesResourceModel = $attributesResourceModel;
        $this->imageProcessor = $imageProcessor;
        $this->targetTypeAttribute = $targetType;
    }

    /**
     * @param BannerDataProvider $bannerDataProvider
     * @param array $loadedData
     * @return array
     */
    public function afterGetData(BannerDataProvider $bannerDataProvider, array $loadedData)
    {
        foreach ($loadedData as $bannerId => $bannerData) {
            $attributes = $this->attributesResourceModel->getBannersAttributes($bannerId);
            $bannerData = array_merge($bannerData, $attributes);
            $bannerData = $this->prepareNavigationImage($bannerId, $bannerData);
            $bannerData = $this->prepareTargetValue($bannerData);
            $loadedData[$bannerId] = $bannerData;
        }
        return $loadedData;
    }

    /**
     * @param array $bannerData
     * @return array
     */
    protected function prepareTargetValue(array $bannerData)
    {
        if (isset($bannerData['target_type'], $bannerData['target_type'])) {
            $typeConfig = $this->targetTypeAttribute->getConfig()->getConfig();
            $targetValue = $this->targetTypeAttribute->getTargetValue(
                $bannerData['target_type'],
                $bannerData['target_id']
            );
            if (isset($typeConfig['types'])) {
                foreach ($typeConfig['types'] as $type) {
                    if (!empty($type['form_map_name'])) {
                        $value = $bannerData['target_type'] == $type['id'] ? $targetValue : null;
                        $bannerData[$type['form_map_name']] = $value;
                    }
                }
            }
        }

        return $bannerData;
    }

    /**
     * @param int $bannerId
     * @param array $bannerData
     * @return mixed
     */
    protected function prepareNavigationImage($bannerId, array $bannerData)
    {
        if (isset($bannerData[NavigationImage::ATTRIBUTE_CODE])) {
            $bannerData[NavigationImage::ATTRIBUTE_CODE] = $this->imageProcessor->getImageForUploader(
                $bannerData[NavigationImage::ATTRIBUTE_CODE],
                NavigationImage::ATTRIBUTE_CODE
            );
        }
        return $bannerData;
    }
}
