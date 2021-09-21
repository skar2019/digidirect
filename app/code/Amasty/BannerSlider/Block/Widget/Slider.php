<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Block\Widget;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Api\Data\SliderInterface;
use Amasty\BannerSlider\Api\SliderRepositoryInterface;
use Amasty\BannerSlider\Model\BannersDataProvider;
use Amasty\BannerSlider\Model\ImageProcessor;
use Amasty\BannerSlider\Model\OptionSource\Alignment;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Slider extends Template implements BlockInterface, IdentityInterface
{
    protected $_template = 'Amasty_BannerSlider::widget/slider.phtml';

    /**
     * @var SliderRepositoryInterface
     */
    private $sliderRepository;

    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var Template\Context
     */
    private $context;

    /**
     * @var BannersDataProvider
     */
    private $bannersDataProvider;

    public function __construct(
        ImageProcessor $imageProcessor,
        SliderRepositoryInterface $sliderRepository,
        Template\Context $context,
        BannersDataProvider $bannersDataProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sliderRepository = $sliderRepository;
        $this->imageProcessor = $imageProcessor;
        $this->context = $context;
        $this->bannersDataProvider = $bannersDataProvider;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return str_replace('\\', '-', $this->getNameInLayout());
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        $identities = [SliderInterface::CACHE_TAG . '_' . $this->getSliderId()];
        foreach ($this->getBanners() as $banner) {
            $identities[] = BannerInterface::CACHE_TAG . '_' . $banner->getId();
        }

        return $identities;
    }

    /**
     * @return SliderInterface|null
     */
    public function getSlider(): ?SliderInterface
    {
        $slider = $this->getData('slider');
        if (!is_object($slider)) {
            try {
                $storeId = (int) $this->context->getStoreManager()->getStore()->getId();
                $slider = $this->sliderRepository->getById((int) $this->getData('slider_id'), $storeId);
                if (!$this->validateSlider($slider)) {
                    $slider = null;
                }
            } catch (NoSuchEntityException $e) {
                $slider = null;
            }
            $this->setData('slider', $slider);
        }

        return $slider;
    }

    private function validateSlider(SliderInterface $slider): bool
    {
        return (bool) $slider->getStatus() && $slider->getBannerIds();
    }

    /**
     * @return BannerInterface[]
     */
    public function getBanners(): array
    {
        $bannersList = $this->getData('banners');
        if (!is_array($bannersList)) {
            $bannersList = $this->getBannerList();
            $this->setData('banners', $bannersList);
        }

        return $bannersList;
    }

    /**
     * @return BannerInterface[]
     */
    private function getBannerList(): array
    {
        $slider = $this->getSlider();

        return $slider ? $this->bannersDataProvider->getBannersListBySlider($slider) : [];
    }

    public function getImages(BannerInterface $banner): array
    {
        return $this->imageProcessor->getImages($banner, $this->getData('slider'));
    }

    public function getOriginalImageSize(BannerInterface $banner): ?array
    {
        $image = $banner->getImage();
        if ($image) {
            $size = $this->imageProcessor->getOriginalImageSize($banner);
        }

        return $size ?? null;
    }

    public function getSliderClass(): string
    {
        $slider = $this->getSlider();

        return sprintf(
            '%s%s%s%s',
            $slider->getNavigationArrows() ? '-ambanner-arrows ' : '',
            $slider->getNavigationBullets() ? '-ambanner-dots ' : '',
            ' -arrows-' . $slider->getArrowsStyle(),
            ' -dots-' . $slider->getBulletsStyle()
        );
    }

    public function getAlignment(): string
    {
        return $this->getData('alignment') ?: Alignment::LEFT;
    }

    public function getAnalyticUrl(string $type): string
    {
        return $this->getUrl(sprintf('ambannerslider/analytic/%s', $type));
    }

    public function getScaledHeight(BannerInterface $banner): ?float
    {
        $slider = $this->getSlider();
        $ratio = $this->getRatio($banner);

        return $ratio ? round(($slider->getBannerWidth() / $ratio)) : null;
    }

    private function getRatio(BannerInterface $banner): ?float
    {
        $size = $this->getOriginalImageSize($banner);

        return $size ? $size[ImageProcessor::WIDTH] / $size[ImageProcessor::HEIGHT] : null;
    }
}
