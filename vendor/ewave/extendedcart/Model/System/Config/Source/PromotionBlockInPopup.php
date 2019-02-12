<?php

namespace Ewave\ExtendedCart\Model\System\Config\Source;

class PromotionBlockInPopup
{
    const NONE = 'none';
    const RELATED = 'related';
    const UPSELL = 'upsell';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('None'),
                'value' => self::NONE,
            ],
            [
                'label' => __('Related'),
                'value' => self::RELATED,
            ],
            [
                'label' => __('Up-sell'),
                'value' => self::UPSELL,
            ],
        ];
    }
}
