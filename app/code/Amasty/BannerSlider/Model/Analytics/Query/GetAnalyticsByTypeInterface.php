<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Analytics\Query;

use Amasty\BannerSlider\Model\Analytics\Analytic;

interface GetAnalyticsByTypeInterface
{
    /**
     * @param string $type
     * @return Analytic[]
     */
    public function execute(string $type): array;
}
