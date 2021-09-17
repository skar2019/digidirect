<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Cache\Tag\Strategy;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Api\Data\SliderInterface;
use Amasty\BannerSlider\Model\ResourceModel\GetAllSlidersWithBanner;
use InvalidArgumentException;
use Magento\Framework\App\Cache\Tag\StrategyInterface;

class Banner implements StrategyInterface
{
    /**
     * @var GetAllSlidersWithBanner
     */
    private $getAllSlidersWithBanner;

    public function __construct(GetAllSlidersWithBanner $getAllSlidersWithBanner)
    {
        $this->getAllSlidersWithBanner = $getAllSlidersWithBanner;
    }

    /**
     * @param object $object
     * @return array
     */
    public function getTags($object): array
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException('Provided argument is not an object');
        }

        if ($object instanceof BannerInterface) {
            $tags = [BannerInterface::CACHE_TAG . '_' . $object->getId()];

            foreach ($this->getAllSlidersWithBanner->execute((int) $object->getId()) as $sliderId) {
                $tags[] = SliderInterface::CACHE_TAG . '_' . $sliderId;
            }

            return $tags;
        }

        return [];
    }
}
