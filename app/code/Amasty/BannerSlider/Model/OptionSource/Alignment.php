<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\OptionSource;

class Alignment implements \Magento\Framework\Option\ArrayInterface
{
    public const CENTER = 'center';

    public const LEFT = 'left';

    public const RIGHT = 'right';

    public function toOptionArray(): array
    {
        return [
            ['value' => self::LEFT, 'label' => __('Left')],
            ['value' => self::CENTER, 'label' => __('Center')],
            ['value' => self::RIGHT, 'label' => __('Right')]
        ];
    }
}
