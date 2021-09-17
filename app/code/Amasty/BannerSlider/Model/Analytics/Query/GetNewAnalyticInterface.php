<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Analytics\Query;

use Amasty\BannerSlider\Api\Data\AnalyticInterface;

interface GetNewAnalyticInterface
{
    public function execute(int $bannerId, string $type): AnalyticInterface;
}
