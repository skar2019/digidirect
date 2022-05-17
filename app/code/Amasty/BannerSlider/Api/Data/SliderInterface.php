<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Api\Data;

interface SliderInterface
{
    public const CACHE_TAG = 'amasty_slider';

    public const STATIC_TABLE_NAME = 'amasty_bannerslider_slider_static';
    public const DYNAMIC_TABLE_NAME = 'amasty_bannerslider_slider_dynamic';
    public const RELATION_TABLE_NAME = 'amasty_bannerslider_slider_banner';

    public const PERSIST_NAME = 'amasty_bannerslider_slider';

    /**#@+
     * Constants defined for keys of data array
     */
    public const ID = 'id';
    public const NAME = 'name';
    public const STATUS = 'status';
    public const AUTOPLAY = 'autoplay';
    public const PAUSE_TIME = 'pause_time';
    public const ANIMATION_EFFECT = 'animation_effect';
    public const TRANSITION_SPEED = 'transition_speed';
    public const NAVIGATION_ARROWS = 'navigation_arrows';
    public const ARROWS_STYLE = 'arrows_style';
    public const NAVIGATION_BULLETS = 'navigation_bullets';
    public const BULLETS_STYLE = 'bullets_style';
    public const BANNER_WIDTH = 'banner_width';
    public const BANNER_HEIGHT = 'banner_height';
    public const IS_LAZY_LOAD_ENABLED = 'is_lazy_load_enabled';
    public const LAZY_LOAD_FIRST_IMAGE = 'lazy_load_first_image';
    public const RESIZE_IMAGES = 'resize_images';
    public const STORE_ID = 'store_id';
    public const MOBILE_WIDTH = 'mobile_width';
    public const MOBILE_HEIGHT = 'mobile_height';
    /**#@-*/

    public const DYNAMIC_FIELDS = [
        self::NAME,
        self::STATUS
    ];

    public const SLIDER_ID = 'slider_id';
    public const BANNER_ID = 'banner_id';
    public const POSITION = 'position';

    public const BANNER_DATA = 'banner_data';
    public const BANNERS = 'banners';
    public const BANNER_IDS = 'banner_ids';
    public const BANNER_NAMES = 'banner_names';
    public const POSITIONS = 'position';

    public const STATIC_FIELDS = [
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
