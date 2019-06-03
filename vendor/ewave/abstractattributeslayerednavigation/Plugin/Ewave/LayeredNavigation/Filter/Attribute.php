<?php

namespace Ewave\AbstractAttributesLayeredNavigation\Plugin\Ewave\LayeredNavigation\Filter;

use Ewave\LayeredNavigation\Helper\FilterSetting;
use Ewave\AbstractAttributesLayeredNavigation\Plugin\Ewave\LayeredNavigation\Model\Source\DisplayMode;

/**
 * Class Attribute
 *
 * @package Ewave\AbstractAttributesLayeredNavigation\Plugin\Ewave\LayeredNavigation\Helper
 */
class Attribute
{
    /**
     * @var \Ewave\AbstractAttributes\Api\OptionRepositoryInterface
     */
    protected $optionRepository;

    /**
     * @var \Ewave\AbstractAttributes\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var FilterSetting
     */
    protected $filterSettingHelper;

    /**
     * Attribute constructor.
     *
     * @param \Ewave\AbstractAttributes\Api\OptionRepositoryInterface $optionRepository
     * @param \Ewave\AbstractAttributes\Helper\Image $imageHelper
     * @param FilterSetting $filterSettingHelper
     * @param array $data
     */
    public function __construct(
        \Ewave\AbstractAttributes\Api\OptionRepositoryInterface $optionRepository,
        \Ewave\AbstractAttributes\Helper\Image $imageHelper,
        \Ewave\LayeredNavigation\Helper\FilterSetting $filterSettingHelper,
        array $data = []
    ) {
        $this->optionRepository = $optionRepository;
        $this->imageHelper = $imageHelper;
        $this->filterSettingHelper = $filterSettingHelper;
    }

    /**
     * @param \Ewave\LayeredNavigation\Model\Layer\Filter\Attribute $subject
     * @param array $items
     * @return array
     */
    public function afterGetItems(\Ewave\LayeredNavigation\Model\Layer\Filter\Attribute $subject, $items)
    {
        $filterSetting = $this->filterSettingHelper->getSettingByLayerFilter($subject);
        if ((int)$filterSetting->getDisplayMode() !== DisplayMode::MODE_IMAGE or empty($items)) {
            return $items;
        }

        foreach ($items as $key => $item) {
            $option = $this->optionRepository->getByOptionId($item->getValue());
            if ($option->getImage()) {
                $item->setImageUrl($this->imageHelper->getOriginalImageUrl($option));
            } else {
                $item->setImageUrl($this->imageHelper->getDefaultPlaceholderUrl('thumbnail'));
            }
            $item->setAbstractAttributeOption($option);
        }
        return $items;
    }
}
