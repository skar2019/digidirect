<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Api\Data;

interface SliderInterface
{
    const CACHE_TAG = 'amasty_slider';

    const STATIC_TABLE_NAME = 'amasty_bannerslider_slider_static';
    const DYNAMIC_TABLE_NAME = 'amasty_bannerslider_slider_dynamic';
    const RELATION_TABLE_NAME = 'amasty_bannerslider_slider_banner';

    const PERSIST_NAME = 'amasty_bannerslider_slider';

    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';
    const NAME = 'name';
    const STATUS = 'status';
    const AUTOPLAY = 'autoplay';
    const PAUSE_TIME = 'pause_time';
    const ANIMATION_EFFECT = 'animation_effect';
    const TRANSITION_SPEED = 'transition_speed';
    const NAVIGATION_ARROWS = 'navigation_arrows';
    const ARROWS_STYLE = 'arrows_style';
    const NAVIGATION_BULLETS = 'navigation_bullets';
    const BULLETS_STYLE = 'bullets_style';
    const BANNER_WIDTH = 'banner_width';
    const BANNER_HEIGHT = 'banner_height';
    const IS_LAZY_LOAD_ENABLED = 'is_lazy_load_enabled';
    const LAZY_LOAD_FIRST_IMAGE = 'lazy_load_first_image';
    const RESIZE_IMAGES = 'resize_images';
    const STORE_ID = 'store_id';
    const MOBILE_WIDTH = 'mobile_width';
    const MOBILE_HEIGHT = 'mobile_height';
    /**#@-*/

    const DYNAMIC_FIELDS = [
        self::NAME,
        self::STATUS
    ];

    const SLIDER_ID = 'slider_id';
    const BANNER_ID = 'banner_id';
    const POSITION = 'position';

    const BANNER_DATA = 'banner_data';
    const BANNERS = 'banners';
    const BANNER_IDS = 'banner_ids';
    const BANNER_NAMES = 'banner_names';
    const POSITIONS = 'position';

    const STATIC_FIELDS = [
        self::ID,
        self::AUTOPLAY,
        self::PAUSE_TIME,
        self::ANIMATION_EFFECT,
        self::TRANSITION_SPEED,
        self::NAVIGATION_ARROWS,
        self::ARROWS_STYLE,
        self::NAVIGATION_BULLETS,
        self::BULLETS_STYLE,
        self::BANNER_WIDTH,
        self::BANNER_HEIGHT,
        self::IS_LAZY_LOAD_ENABLED,
        self::LAZY_LOAD_FIRST_IMAGE,
        self::MOBILE_WIDTH,
        self::MOBILE_HEIGHT,
        self::RESIZE_IMAGES
    ];

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\BannerSlider\Api\Data\SliderInterface
     */
    public function setId($id);

    /**
     * @return int|null
     */
    public function getAutoplay();

    /**
     * @param int|null $autoplay
     *
     * @return \Amasty\BannerSlider\Api\Data\SliderInterface
     */
    public function setAutoplay($autoplay);

    /**
     * @return int|null
     */
    public function getPauseTime();

    /**
     * @param int|null $pauseTime
     *
     * @return \Amasty\BannerSlider\Api\Data\SliderInterface
     */
    public function setPauseTime($pauseTime);

    /**
     * @return int
     */
    public function getAnimationEffect();

    /**
     * @param int $animationEffect
     *
     * @return \Amasty\BannerSlider\Api\Data\SliderInterface
     */
    public function setAnimationEffect($animationEffect);

    /**
     * @return int|null
     */
    public function getTransitionSpeed();

    /**
     * @param int|null $transitionSpeed
     *
     * @return \Amasty\BannerSlider\Api\Data\SliderInterface
     */
    public function setTransitionSpeed($transitionSpeed);

    /**
     * @return int|null
     */
    public function getNavigationArrows();

    /**
     * @param int|null $navigationArrows
     *
     * @return \Amasty\BannerSlider\Api\Data\SliderInterface
     */
    public function setNavigationArrows($navigationArrows);

    /**
     * @return int
     */
    public function getArrowsStyle();

    /**
     * @param int $arrowsStyle
     *
     * @return \Amasty\BannerSlider\Api\Data\SliderInterface
     */
    public function setArrowsStyle($arrowsStyle);

    /**
     * @return int|null
     */
    public function getNavigationBullets();

    /**
     * @param int|null $navigationBullets
     *
     * @return \Amasty\BannerSlider\Api\Data\SliderInterface
     */
    public function setNavigationBullets($navigationBullets);

    /**
     * @return int
     */
    public function getBulletsStyle();

    /**
     * @param int $bulletsStyle
     *
     * @return \Amasty\BannerSlider\Api\Data\SliderInterface
     */
    public function setBulletsStyle($bulletsStyle);

    /**
     * @return int
     */
    public function getBannerWidth();

    /**
     * @param int $bannerWidth
     *
     * @return \Amasty\BannerSlider\Api\Data\SliderInterface
     */
    public function setBannerWidth($bannerWidth);

    /**
     * @return int
     */
    public function getBannerHeight();

    /**
     * @param int $bannerHeight
     *
     * @return \Amasty\BannerSlider\Api\Data\SliderInterface
     */
    public function setBannerHeight($bannerHeight);

    public function isLazyLoadEnabled(): ?bool;

    public function setIsLazyLoadEnabled(bool $isLazyLoadEnabled): void;

    public function isLazyLoadFirstImage(): ?bool;

    public function setIsLazyLoadFirstImage(bool $isLazyLoadFirstImage): void;

    public function getMobileWidth(): ?int;

    public function setMobileWidth(int $bannerWidth): void;

    public function getMobileHeight(): ?int;

    public function setMobileHeight(int $bannerHeight): void;

    /**
     * @return int|null
     */
    public function getResizeImages();

    /**
     * @param int|null $resizeImages
     *
     * @return \Amasty\BannerSlider\Api\Data\SliderInterface
     */
    public function setResizeImages($resizeImages);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $bannerHeight
     *
     * @return \Amasty\BannerSlider\Api\Data\SliderInterface
     */
    public function setStoreId($storeId);
}
