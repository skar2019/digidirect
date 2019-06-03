<?php
namespace Ewave\AbstractAttributesLayeredNavigation\Plugin\Ewave\LayeredNavigation\Model\Source;

class DisplayMode
{
    /**
     * MODE_SLIDER
     */
    const MODE_IMAGE = 3;

    /**
     * @param \Ewave\LayeredNavigation\Model\Source\DisplayMode $subject
     * @param array $options
     * @return array
     */
    public function afterToOptionArray(\Ewave\LayeredNavigation\Model\Source\DisplayMode $subject, $options)
    {
        $options[] = [
            'value' => self::MODE_IMAGE,
            'label' => __('AA Image'),
        ];

        return $options;
    }

    /**
     * @param \Ewave\LayeredNavigation\Model\Source\DisplayMode $subject
     * @param array $options
     * @return array
     */
    public function afterToArray(\Ewave\LayeredNavigation\Model\Source\DisplayMode $subject, $options)
    {
        $options[self::MODE_IMAGE] = __('AA Image');
        return $options;
    }
}
