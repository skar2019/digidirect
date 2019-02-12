<?php

namespace Ewave\Collect\Model\Config\Source;

use Ewave\Collect\Helper\Data as CollectHelper;

/**
 * Class Variation
 *
 * @package Ewave\Collect\Model\Config\Source
 */
class Variation implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => CollectHelper::VARIATION_TYPE_FULL, 'label' => __('Full C&C')],
            ['value' => CollectHelper::VARIATION_TYPE_SINGLE, 'label' => __('Single Pickup Location')],
            ['value' => CollectHelper::VARIATION_TYPE_CART_SINGLE, 'label' => __('Single C&C for Cart')],
        ];
    }
}
