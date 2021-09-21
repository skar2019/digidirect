<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Api\Data\SliderInterface;
use Amasty\BannerSlider\Model\OptionSource\Banner\VisibleOn;
use Amasty\BannerSlider\Model\Repository\BannerRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SortOrderBuilderFactory;

class BannersDataProvider
{
    /**
     * @var BannerRepository
     */
    private $bannerRepository;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var SortOrderBuilderFactory
     */
    private $sortOrderBuilderFactory;

    /**
     * @var MobileDetect
     */
    private $mobileDetect;

    public function __construct(
        BannerRepository $bannerRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        SortOrderBuilderFactory $sortOrderBuilderFactory,
        MobileDetect $mobileDetect
    ) {
        $this->bannerRepository = $bannerRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->sortOrderBuilderFactory = $sortOrderBuilderFactory;
        $this->mobileDetect = $mobileDetect;
    }

    /**
     * @param SliderInterface $slider
     * @return BannerInterface[]
     */
    public function getBannersListBySlider(SliderInterface $slider): array
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder * */
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $this->addFilters($slider, $searchCriteriaBuilder);
        $banners = $this->bannerRepository->getOrderedBannerList(
            $searchCriteriaBuilder->create(),
            $slider->getBannerIds()
        );

        return $this->bannerRepository->validateBanners($banners);
    }

    private function getVisibleOnDetect(): array
    {
        return $this->mobileDetect->isMobile()
            ? [VisibleOn::ALL, VisibleOn::MOBILE]
            : [VisibleOn::ALL, VisibleOn::DESKTOP];
    }

    public function addFilters(SliderInterface $slider, SearchCriteriaBuilder $searchCriteriaBuilder): void
    {
        $searchCriteriaBuilder->addFilter(
            BannerInterface::ID,
            $slider->getBannerIds(),
            'in'
        );
        $searchCriteriaBuilder->addFilter(
            BannerInterface::VISIBLE_ON,
            $this->getVisibleOnDetect(),
            'in'
        );
    }
}
