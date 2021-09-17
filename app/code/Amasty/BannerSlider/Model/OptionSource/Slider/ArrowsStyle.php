<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\OptionSource\Slider;

use Magento\Framework\Option\ArrayInterface;

class ArrowsStyle implements ArrayInterface
{
    const FIRST = 1;

    const SECOND = 2;

    const THIRD = 3;

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::FIRST,
                'label' => __('Round Open')
            ],
            [
                'value' => self::SECOND,
                'label' => __('Square Classic')
            ],
            [
                'value' => self::THIRD,
                'label' => __('Round Filled')
            ]
        ];
    }
}
