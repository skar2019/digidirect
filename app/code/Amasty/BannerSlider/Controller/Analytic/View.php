<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Controller\Analytic;

use Amasty\BannerSlider\Model\Analytics\Temp\TempEntity;

class View extends Ctr
{
    protected function getType(): string
    {
        return TempEntity::VIEW_TYPE;
    }
}
