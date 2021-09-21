<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Cron;

use Amasty\BannerSlider\Api\Data\SliderInterface;
use Amasty\BannerSlider\Model\ResourceModel\Banner\Collection as BannerCollection;
use Amasty\BannerSlider\Model\ResourceModel\Slider\Grid\Collection as SliderCollection;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Indexer\CacheContext;

class CleanCache
{
    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var CacheContext
     */
    private $cacheContext;

    /**
     * @var BannerCollection
     */
    private $bannerCollection;

    /**
     * @var SliderCollection
     */
    private $sliderCollection;

    public function __construct(
        BannerCollection $bannerCollection,
        SliderCollection $sliderCollection,
        ManagerInterface $eventManager,
        CacheContext $cacheContext
    ) {
        $this->eventManager = $eventManager;
        $this->cacheContext = $cacheContext;
        $this->bannerCollection = $bannerCollection;
        $this->sliderCollection = $sliderCollection;
    }

    public function execute(): void
    {
        $banners = $this->bannerCollection->getBannersForScheduler()->getAllIds();
        $sliders = $this->sliderCollection->addBannerFilter($banners);

        $this->cacheContext->registerEntities(SliderInterface::CACHE_TAG, $sliders->getAllIds());
        $this->eventManager->dispatch('clean_cache_by_tags', ['object' => $this->cacheContext]);
    }
}
