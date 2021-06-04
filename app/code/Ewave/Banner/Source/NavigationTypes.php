<?php

namespace Ewave\Banner\Source;

class NavigationTypes implements \Magento\Framework\Option\ArrayInterface
{
    const NAVIGATION_TYPE_TITLE_WYSIWYG = 1;
    const NAVIGATION_TYPE_TITLE_IMAGE = 2;

    /**
     * @return array
     */
    public function getNavigationTypes()
    {
        return [
            self::NAVIGATION_TYPE_TITLE_WYSIWYG => __('Title'),
            self::NAVIGATION_TYPE_TITLE_IMAGE => __('Image'),
        ];
    }

    /**
     * Get types as a source model result
     *
     * @return array
     */
    public function toOptionArray()
    {
        $types = $this->getNavigationTypes();
        $result = [];
        foreach ($types as $key => $label) {
            $result[] = ['value' => $key, 'label' => __($label)];
        }
        return $result;
    }
}
