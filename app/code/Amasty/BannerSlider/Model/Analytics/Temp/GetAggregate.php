<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Analytics\Temp;

use Amasty\BannerSlider\Model\ResourceModel\Analytic\Temp\GetAggregate as GetAggregateResource;
use Exception;
use Psr\Log\LoggerInterface;

class GetAggregate
{
    /**
     * @var GetAggregateResource
     */
    private $getAggregateResource;

    /**
     * @var AggregateTempResultFactory
     */
    private $aggregateTempResultFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        GetAggregateResource $getAggregateResource,
        AggregateTempResultFactory $aggregateTempResultFactory,
        LoggerInterface $logger
    ) {
        $this->getAggregateResource = $getAggregateResource;
        $this->aggregateTempResultFactory = $aggregateTempResultFactory;
        $this->logger = $logger;
    }

    public function execute(int $bannerId, int $versionId, string $type): ?AggregateTempResult
    {
        $result = null;

        try {
            $aggregatedData = $this->getAggregateResource->execute($bannerId, $versionId, $type);
            if ($aggregatedData) {
                $result = $this->aggregateTempResultFactory->create(['data' => $aggregatedData]);
            }
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $result;
    }
}
