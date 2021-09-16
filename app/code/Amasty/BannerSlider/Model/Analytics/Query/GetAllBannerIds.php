<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Analytics\Query;

use Amasty\BannerSlider\Model\ResourceModel\Banner\Collection;
use Amasty\BannerSlider\Model\ResourceModel\Banner\CollectionFactory;

class GetAllBannerIds implements GetAllBannerIdsInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function execute(): array
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        return $collection->getAllIds();
    }
}
