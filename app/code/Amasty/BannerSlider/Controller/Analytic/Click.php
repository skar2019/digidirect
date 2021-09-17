<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Controller\Analytic;

use Amasty\BannerSlider\Model\Analytics\Temp\TempEntity;

class Click extends Ctr
{
    protected function getType(): string
    {
        return TempEntity::CLICK_TYPE;
    }
}
