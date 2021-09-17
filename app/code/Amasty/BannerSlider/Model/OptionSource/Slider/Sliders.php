<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\OptionSource\Slider;

use Magento\Framework\Data\OptionSourceInterface;
use Amasty\BannerSlider\Api\SliderRepositoryInterface;

class Sliders implements OptionSourceInterface
{
    /**
     * @var SliderRepositoryInterface
     */
    private $sliderRepository;

    public function __construct(
        SliderRepositoryInterface $sliderRepository
    ) {
        $this->sliderRepository = $sliderRepository;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $categories = [];
        foreach ($this->sliderRepository->getAllSliders() as $slider) {
            $categories[] = [
                'value' => $slider->getId(),
                'label' => $slider->getName()
            ];
        }

        return $categories;
    }
}
