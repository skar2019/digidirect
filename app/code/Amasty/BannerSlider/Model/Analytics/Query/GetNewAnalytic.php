<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Analytics\Query;

use Amasty\BannerSlider\Api\Data\AnalyticInterface;
use Amasty\BannerSlider\Api\Data\AnalyticInterfaceFactory;

class GetNewAnalytic implements GetNewAnalyticInterface
{
    /**
     * @var AnalyticInterfaceFactory
     */
    private $analyticFactory;

    public function __construct(AnalyticInterfaceFactory $analyticFactory)
    {
        $this->analyticFactory = $analyticFactory;
    }

    public function execute(int $bannerId, string $type): AnalyticInterface
    {
        /** @var AnalyticInterface $analytic */
        $analytic = $this->analyticFactory->create();
        $analytic->setCounter(0);
        $analytic->setVersionId(0);
        $analytic->setBannerId($bannerId);
        $analytic->setType($type);

        return $analytic;
    }
}
