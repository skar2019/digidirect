<?php

declare(strict_types=1);

namespace Amasty\BannerSliderGraphql\Model\Resolver;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Api\Data\SliderInterface;
use Amasty\BannerSlider\Api\SliderRepositoryInterface;
use Amasty\BannerSlider\Model\BannersDataProvider;
use Amasty\BannerSlider\Model\ImageProcessor;
use Amasty\BannerSlider\Model\OptionSource\Slider\AnimationEffect;
use Magento\Store\Api\Data\StoreInterface;

class SliderDataProvider
{
    /**
     * @var BannersDataProvider
     */
    private $bannersDataProvider;

    /**
     * @var SliderRepositoryInterface
     */
    private $sliderRepository;

    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var AnimationEffect
     */
    private $animationEffect;

    public function __construct(
        SliderRepositoryInterface $sliderRepository,
        BannersDataProvider $bannersDataProvider,
        ImageProcessor $imageProcessor,
        AnimationEffect $animationEffect
    ) {
        $this->bannersDataProvider = $bannersDataProvider;
        $this->sliderRepository = $sliderRepository;
        $this->imageProcessor = $imageProcessor;
        $this->animationEffect = $animationEffect;
    }

    public function execute(int $id, StoreInterface $store): array
    {
        $slider = $this->sliderRepository->getById($id, (int)$store->getId());
        $data = $slider->getData();
        $data[SliderInterface::ANIMATION_EFFECT] = $this->prettyAnimation((string)$slider->getAnimationEffect());
        $data['banners'] = $this->preparebanners(
            $this->bannersDataProvider->getBannersListBySlider($slider),
            $slider,
            $store
        );

        return $data;
    }

    private function prepareBanners(array $banners, SliderInterface $slider, StoreInterface $store): array
    {
        $data = [];

        foreach ($banners as $banner) {
            /** @var BannerInterface $banner */
            $data[$banner->getId()] = $banner->getData();
            $data[$banner->getId()]['image_path'] = $this->getRelativePath(
                $this->imageProcessor->getImageSrc($banner, $slider),
                $store
            );
        }

        return $data;
    }

    private function getRelativePath(string $url, $store): string
    {
        $baseUrl = trim($store->getBaseUrl(), '/');

        return str_replace($baseUrl, '', $url);
    }

    private function prettyAnimation(string $animation): string
    {
        foreach ($this->animationEffect->toOptionArray() as $item) {
            if ($item['value'] == $animation) {
                $animation = $item['code'] ?? $animation;
            }
        }

        return $animation;
    }
}
