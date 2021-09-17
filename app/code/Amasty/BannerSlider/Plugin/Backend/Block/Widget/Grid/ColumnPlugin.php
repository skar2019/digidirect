<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Plugin\Backend\Block\Widget\Grid;

use Amasty\BannerSlider\Api\Data\SliderInterface;
use Magento\Backend\Block\Widget\Grid\Column;

class ColumnPlugin
{
    public function afterGetFilterHtml(Column $subject, string $result): string
    {
        if ($subject->getId() == SliderInterface::BANNER_NAMES) {
            $result = '';
        }

        return $result;
    }
}
