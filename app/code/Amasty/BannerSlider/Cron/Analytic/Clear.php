<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Cron\Analytic;

use Amasty\BannerSlider\Model\ResourceModel\Analytic\Clear as ClearResource;

class Clear
{
    /**
     * @var ClearResource
     */
    private $clearResource;

    public function __construct(ClearResource $clearResource)
    {
        $this->clearResource = $clearResource;
    }

    public function execute(): void
    {
        $this->clearResource->execute();
    }
}
