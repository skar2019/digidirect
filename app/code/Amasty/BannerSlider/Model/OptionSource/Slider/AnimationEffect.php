<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\OptionSource\Slider;

use Magento\Framework\Option\ArrayInterface;

class AnimationEffect implements ArrayInterface
{
    public const ROLLING = 0;

    public const SPLIT = 1;

    public const BUBBLE = 2;

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::ROLLING,
                'code' => 'rolling', //GraphQl Compatibility
                'label' => __('Rolling')
            ],
            [
                'value' => self::SPLIT,
                'code' => 'split',//GraphQl Compatibility
                'label' => __('Split Slideshow')
            ],
            [
                'value' => self::BUBBLE,
                'code' => 'bubble',//GraphQl Compatibility
                'label' => __('Bubble')
            ]
        ];
    }
}
