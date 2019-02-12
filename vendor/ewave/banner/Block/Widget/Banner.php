<?php

namespace Ewave\Banner\Block\Widget;

use Ewave\Banner\Block\Widget\StaticWidget\TypeRendererInterface;
use Ewave\Banner\Helper\IssetTrait;
use Ewave\Banner\Preference\Magento\Banner\Model\Banner\Data;
use Magento\Framework\App\ObjectManager;
use Ewave\Banner\Inheritance\Magento\Banner\Block\Widget\Banner as BannerInheritance;

/**
 * Class Banner
 *
 */
class Banner extends BannerInheritance
{
    use IssetTrait;

    /**
     * Return banner delay option
     *
     * @return string
     */
    public function getDelaySpeedValue()
    {
        return $this->getData('rotation_speed');
    }

    /**
     * @return bool
     */
    public function isLoop()
    {
        return (bool)$this->getData('loop');
    }

    /**
     * @return string
     */
    public function getBreadcrumbsType()
    {
        return $this->getData('breadcrumbs_type');
    }

    /**
     * @return bool
     */
    public function isAutoplay()
    {
        return (bool)$this->getData('autoplay');
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $customTemplate = trim($this->getData('custom_template'));
        if ($customTemplate) {
            $templatePath = $this->getData('custom_template') . '.phtml';
            $moduleName = $this->getModuleName();
            $params = ['module' => $moduleName];
            $area = $this->getArea();
            if ($area) {
                $params['area'] = $area;
            }
            $path = $this->resolver->getTemplateFileName(
                $moduleName . '::widget/' . $templatePath,
                $params
            );

            if ($path) {
                $this->setTemplate($moduleName . '::widget/' . $templatePath);
            }
        }
        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getBannersJson()
    {
        return $this->jsonComponent->encode($this->getBannersArray());
    }

    /**
     * @return array|mixed|null
     */
    public function getBannersArray()
    {
        if (null === $this->banners) {
            $this->banners = $this->getDataModel()->getSectionData();
        }

        return $this->banners;
    }

    /**
     * @return Data
     */
    private function getDataModel()
    {
        return ObjectManager::getInstance()->get(Data::class);
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $cacheKey = parent::getCacheKeyInfo();
        foreach ($this->getData() as $key => $value) {
            if (!is_scalar($value)) {
                continue;
            }
            $cacheKey[$key] = $value;
        }
        $cacheKey['nil'] = $this->getNameInLayout();
        $cacheKey['data_cache'] = $this->getDataModel()->getCacheKey() . 'data_cache';
        return $cacheKey;
    }

    /**
     * @return $this
     */
    public function forceNoLifetime()
    {
        $this->setData(static::NO_LIFETIME, true);
        return $this;
    }

    /**
     * @return bool|int|null
     */
    public function getCacheLifetime()
    {
        if ($this->getData(static::NO_LIFETIME)) {
            return null;
        }
        $lifetime = parent::getCacheLifetime();
        if (!$lifetime) {
            $lifetime = 86400;
        }
        return $lifetime;
    }

    /**
     * @param $bannersToDisplay
     * @return array
     */
    public function getFixedBanners($bannersToDisplay)
    {
        $bannerIds = $this->getBannerIds();
        if (!$bannerIds) {
            $bannersToDisplay = [];
        }
        $bannerIds = explode(',', $bannerIds);

        $sortedBanners = [];
        foreach ($bannersToDisplay as $key => $banner) {
            if (in_array($key, $bannerIds)) {
                $position = array_search($key, $bannerIds);
                $sortedBanners[$position] = $banner;
            }
            unset($bannersToDisplay[$key]);
        }

        ksort($sortedBanners);
        return $sortedBanners;
    }

    /**
     * @return array
     */
    public function getBannersToDisplay()
    {
        $displayMode = $this->getDisplayMode();
        $bannersArray = $this->getBannersArray();
        $bannerItems = $this->getByKey($bannersArray, 'items', []);
        $bannersToDisplay = $this->getByKey($bannerItems, $displayMode, []);
        if (!empty($bannersToDisplay)) {
            switch ($displayMode) {
                case 'fixed':
                    $bannersToDisplay = $this->getFixedBanners($bannersToDisplay);
                    break;
                default:
                    break;
            }
        }
        return $bannersToDisplay;
    }

    /**
     * @param array $bannerItem
     * @return bool
     */
    public function isVideoBannerItem(array $bannerItem)
    {
        return $this->hasVideo($bannerItem);
    }

    /**
     * @param array $bannerItem
     * @return bool
     */
    public function hasVideo(array $bannerItem)
    {
        $video = $this->videoHelper->get($bannerItem);
        return !empty($video);
    }

    /**
     * @param array $bannerItem
     * @return bool
     */
    public function isContentBanner(array $bannerItem)
    {
        return !$this->hasImages($bannerItem) && !$this->hasVideo($bannerItem);
    }

    /**
     * @param array $bannerItem
     * @return bool
     */
    public function isMediaBannerItem(array $bannerItem)
    {
        return $this->hasVideo($bannerItem) && $this->hasImages($bannerItem);
    }

    /**
     * @param array $bannerItem
     * @return bool
     */
    public function hasImages(array $bannerItem)
    {
        $images = $this->imageHelper->get($bannerItem);
        return !empty($images);
    }

    /**
     * @param array $bannerItem
     * @return bool
     */
    public function isImageBanner(array $bannerItem)
    {
        return !$this->hasVideo($bannerItem) && $this->hasImages($bannerItem);
    }

    /**
     * @param array $callbackConfiguration
     * @param array $bannerItem
     * @return null|string
     */
    protected function walkCallback(array $callbackConfiguration, array $bannerItem)
    {
        $result = null;
        foreach ($callbackConfiguration as $key => $callbackItself) {
            try {
                if (is_callable($callbackItself)) {
                    $result = $callbackItself($bannerItem);
                } else {
                    $result = $this->$callbackItself($bannerItem);
                }
                if (!$result) {
                    continue;
                }

                return $key;
            } catch (\Throwable $exception) {
                $result = null;
            }
        }

        return $result;
    }

    /**
     * @param array $bannerItem
     * @return mixed|null
     */
    public function getBannerType(array $bannerItem)
    {
        $result = $this->walkCallback($this->customCallbacks, $bannerItem);
        if (!$result) {
            $result = $this->walkCallback($this->callbacks, $bannerItem);
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function widgetHasVideo()
    {
        $bannersToDisplay = $this->getBannersToDisplay();
        if (!empty($bannersToDisplay)) {
            foreach ($bannersToDisplay as $banner) {
                if ($this->isVideoBannerItem($banner)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $type
     * @return mixed|null
     */
    protected function getBannerRendererByType($type)
    {
        return $this->getByKey($this->rendererConfiguration, $type);
    }

    /**
     * @param array $bannerItem
     * @return string
     */
    public function renderBannerItem(array $bannerItem)
    {
        $type = $this->getBannerType($bannerItem);
        $rendererConfiguration = $this->getBannerRendererByType($type);
        if (!$rendererConfiguration) {
            return '';
        }
        $template = $this->getByKey($rendererConfiguration, TypeRendererInterface::TEMPLATE);
        $className = $this->getByKey($rendererConfiguration, TypeRendererInterface::CLASS_NAME);
        if (!$template || !$className) {
            return '';
        }
        /**
         * @var $renderer TypeRendererInterface
         */
        $renderer = $this->objectManager->get($className);
        if (!($renderer instanceof TypeRendererInterface)) {
            return '';
        }

        return $renderer->setWidget($this)->setBanner($bannerItem)->setBannerTemplate($template)->renderBanner();
    }

    /**
     * @param array $configuration
     * @return Banner
     */
    public function addCallbackResult(array $configuration = [])
    {
        foreach ($configuration as $callbackName => $type) {
            if (is_callable($type)) {
                $this->customCallbacks[$callbackName] = $type;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getMediaQuery()
    {
        $mediaQueryFull = [];
        foreach ($this->getBannersToDisplay() as $bannerItem) {
            $images = $this->imageHelper->get($bannerItem);
            $videos = $this->videoHelper->get($bannerItem);
            if (!empty($videos)) {
                foreach ($videos as $video) {
                    $mediaQueries = $this->videoHelper->getVideoAttribute($video, 'media_query');
                    if (is_array($mediaQueries)) {
                        foreach ($mediaQueries as $media) {
                            $this->addToMediaQuery($mediaQueryFull, $media);
                        }
                    }
                }
            }
            foreach ($images as $image) {
                $media = $this->imageHelper->getImageAttribute($image, 'media');
                $this->addToMediaQuery($mediaQueryFull, $media);
            }
        }
        return array_values($mediaQueryFull);
    }

    /**
     * @param $queriesArray
     * @param $query
     */
    protected function addToMediaQuery(&$queriesArray, $query)
    {
        if ($query && !isset($queriesArray[$query])) {
            $queriesArray[$query] = $query;
        }
    }

    /**
     * @param array $value
     * @return string
     */
    public function jsonEncode(array $value)
    {
        return $this->jsonComponent->encode($value);
    }

    /**
     * @param string $value
     * @return mixed
     */
    public function jsonDecode($value)
    {
        return $this->jsonComponent->decode($value);
    }

    /**
     * @return int|string|bool
     */
    public function getIsLazyLoad()
    {
        return $this->getData('lazy_load');
    }

    /**
     * @return int|string|bool
     */
    public function getArrowPrevious()
    {
        return $this->getData('arrow_previous');
    }

    /**
     * @return int|string|bool
     */
    public function getArrowNext()
    {
        return $this->getData('arrow_next');
    }
}
