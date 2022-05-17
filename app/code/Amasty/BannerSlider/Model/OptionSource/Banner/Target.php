<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\OptionSource\Banner;

use Magento\Framework\Option\ArrayInterface;

class Target implements ArrayInterface
{
    public const BLANK = '_blank';

    public const SELF = '_self';

    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::BLANK,
                'label' => __('Open on a new page')
            ],
            [
                'value' => self::SELF,
                'label' => __('Open on the same page')
            ]
        ];
    }
}
