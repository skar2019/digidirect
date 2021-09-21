<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Analytics\Command;

use Amasty\BannerSlider\Api\Data\AnalyticInterface;
use Amasty\BannerSlider\Model\Analytics\Analytic;
use Amasty\BannerSlider\Model\ResourceModel\Analytic as AnalyticResource;
use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;

class SaveAnalytic implements SaveAnalyticInterface
{
    /**
     * @var AnalyticResource
     */
    private $analyticResource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        AnalyticResource $analyticResource,
        LoggerInterface $logger
    ) {
        $this->analyticResource = $analyticResource;
        $this->logger = $logger;
    }

    /**
     * @param AnalyticInterface|Analytic $analytic
     * @return void
     * @throws CouldNotSaveException
     */
    public function execute(AnalyticInterface $analytic): void
    {
        try {
            $this->analyticResource->save($analytic);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save Analytic'), $e);
        }
    }
}
