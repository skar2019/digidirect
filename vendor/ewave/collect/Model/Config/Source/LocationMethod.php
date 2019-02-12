<?php

namespace Ewave\Collect\Model\Config\Source;

use Ewave\Collect\Helper\Data as CollectHelper;

/**
 * Class LocationMethod
 *
 * @package Ewave\Collect\Model\Config\Source
 */
class LocationMethod implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => CollectHelper::GEO_LOCATION_METHOD_GOOGLE_API, 'label' => __('Google API')],
            ['value' => CollectHelper::GEO_LOCATION_METHOD_POST_CODE, 'label' => __('AUPost post code file')],
        ];
    }
}
