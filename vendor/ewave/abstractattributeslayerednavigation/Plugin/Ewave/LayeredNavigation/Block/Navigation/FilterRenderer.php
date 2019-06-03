<?php
namespace Ewave\AbstractAttributesLayeredNavigation\Plugin\Ewave\LayeredNavigation\Block\Navigation;

use Ewave\AbstractAttributesLayeredNavigation\Plugin\Ewave\LayeredNavigation\Model\Source\DisplayMode;
use Ewave\LayeredNavigation\Api\Data\FilterSettingInterface;
use Ewave\LayeredNavigation\Block\Navigation\FilterRenderer as Subject;

class FilterRenderer
{
    const LAYER_FILTER_IMAGE_TEMPLATE = 'Ewave_AbstractAttributesLayeredNavigation::layer/filter/image.phtml';

    /**
     * @param Subject $subject
     * @param callable $proceed
     * @param FilterSettingInterface $filterSetting
     * @return string
     */
    public function aroundGetTemplateByFilterSetting(
        Subject $subject,
        callable $proceed,
        FilterSettingInterface $filterSetting
    ) {
        if ((int)$filterSetting->getDisplayMode() === DisplayMode::MODE_IMAGE) {
            return self::LAYER_FILTER_IMAGE_TEMPLATE;
        }

        return $proceed($filterSetting);
    }
}
