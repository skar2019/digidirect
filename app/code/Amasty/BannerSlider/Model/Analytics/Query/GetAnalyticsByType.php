<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Analytics\Query;

use Amasty\BannerSlider\Api\Data\AnalyticInterface;
use Amasty\BannerSlider\Model\ResourceModel\Analytic\Collection;
use Amasty\BannerSlider\Model\ResourceModel\Analytic\CollectionFactory;

class GetAnalyticsByType implements GetAnalyticsByTypeInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Return analytics by type in array where key is Banner ID.
     *
     * @param string $type
     * @return AnalyticInterface[]
     */
    public function execute(string $type): array
    {
        $result = [];

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(AnalyticInterface::TYPE, $type);
        /** @var AnalyticInterface $analytic */
        foreach ($collection->getItems() as $analytic) {
            $result[$analytic->getBannerId()] = $analytic;
        }

        return $result;
    }
}
