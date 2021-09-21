<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Cron\Analytic;

use Amasty\BannerSlider\Model\Analytics\Command\SaveAnalyticInterface;
use Amasty\BannerSlider\Model\Analytics\Query\GetAllBannerIdsInterface;
use Amasty\BannerSlider\Model\Analytics\Query\GetAnalyticsByTypeInterface;
use Amasty\BannerSlider\Model\Analytics\Query\GetNewAnalyticInterface;
use Amasty\BannerSlider\Model\Analytics\Temp\GetAggregate;
use Magento\Framework\Exception\CouldNotSaveException;

class Collector
{
    /**
     * @var GetAllBannerIdsInterface
     */
    private $getAllBannerIds;

    /**
     * @var GetAnalyticsByTypeInterface
     */
    private $getAnalyticsByType;

    /**
     * @var GetNewAnalyticInterface
     */
    private $getNewAnalytic;

    /**
     * @var GetAggregate
     */
    private $getAggregate;

    /**
     * @var SaveAnalyticInterface
     */
    private $saveAnalyticCommand;

    /**
     * @var string
     */
    private $type;

    public function __construct(
        GetAllBannerIdsInterface $getAllBannerIds,
        GetAnalyticsByTypeInterface $getAnalyticsByType,
        GetNewAnalyticInterface $getNewAnalytic,
        GetAggregate $getAggregate,
        SaveAnalyticInterface $saveAnalyticCommand,
        string $type = ''
    ) {
        $this->getAllBannerIds = $getAllBannerIds;
        $this->getAnalyticsByType = $getAnalyticsByType;
        $this->getNewAnalytic = $getNewAnalytic;
        $this->getAggregate = $getAggregate;
        $this->saveAnalyticCommand = $saveAnalyticCommand;
        $this->type = $type;
    }

    public function execute(): void
    {
        $bannerIds = $this->getAllBannerIds->execute();
        $analytics = $this->getAnalyticsByType->execute($this->type);

        foreach ($bannerIds as $bannerId) {
            $analytic = $analytics[$bannerId] ?? $this->getNewAnalytic->execute((int) $bannerId, $this->type);
            $aggregatedResult = $this->getAggregate->execute(
                (int) $bannerId,
                $analytic->getVersionId(),
                $analytic->getType()
            );

            if ($aggregatedResult) {
                $analytic->setCounter($analytic->getCounter() + $aggregatedResult->getCounter());
                $analytic->setVersionId($aggregatedResult->getVersionId());

                try {
                    $this->saveAnalyticCommand->execute($analytic);
                } catch (CouldNotSaveException $e) {
                    null;
                }
            }
        }
    }
}
