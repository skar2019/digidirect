<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\OptionSource;

class Alignment implements \Magento\Framework\Option\ArrayInterface
{
    const CENTER = 'center';

    const LEFT = 'left';

    const RIGHT = 'right';

    public function toOptionArray(): array
    {
        return [
            ['value' => self::LEFT, 'label' => __('Left')],
            ['value' => self::CENTER, 'label' => __('Center')],
            ['value' => self::RIGHT, 'label' => __('Right')]
        ];
    }
}
