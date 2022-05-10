<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\OptionSource\Slider;

use Magento\Framework\Option\ArrayInterface;

class BulletsStyle implements ArrayInterface
{
    public const FIRST = 1;

    public const SECOND = 2;

    public const THIRD = 3;

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::FIRST,
                'label' => __('Morse')
            ],
            [
                'value' => self::SECOND,
                'label' => __('Line')
            ],
            [
                'value' => self::THIRD,
                'label' => __('Round')
            ]
        ];
    }
}
