<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Api\Data;

interface BannerInterface
{
    public const CACHE_TAG = 'amasty_banner';

    public const STATIC_TABLE_NAME = 'amasty_bannerslider_banner_static';
    public const DYNAMIC_TABLE_NAME = 'amasty_bannerslider_banner_dynamic';

    public const PERSIST_NAME = 'amasty_bannerslider_banner';

    /**#@+
     * Constants defined for keys of data array
     */
    public const ID = 'id';
    public const NAME = 'name';
    public const CUSTOMER_GROUP = 'customer_group';
    public const STATUS = 'status';
    public const VISIBLE_ON = 'visible_on';
    public const TARGET_TYPE = 'target_type';
    public const IMAGE = 'image';
    public const IMAGE_ALT = 'image_alt';
    public const TARGET_URL = 'target_url';
    public const HOVER_TEXT = 'hover_text';
    public const STORE_ID = 'store_id';
    public const START_DATE = 'start_date';
    public const END_DATE = 'end_date';
    /**#@-*/

    public const STATIC_FIELDS = [
        self::NAME,
        self::VISIBLE_ON,
        self::TARGET_TYPE,
        self::CUSTOMER_GROUP
    ];

    public const DYNAMIC_FIELDS = [
        self::STATUS,
        self::IMAGE,
        self::IMAGE_ALT,
        self::TARGET_URL,
        self::HOVER_TEXT
    ];

    public function getId();

    public function setId($id): BannerInterface;

    public function getName(): string;

    public function setName(string $name): BannerInterface;

    public function getCustomerGroup(): string;

    public function setCustomerGroup(string $customerGroup): BannerInterface;

    public function getStatus(): bool;

    public function setStatus(bool $status): BannerInterface;

    public function getImage(): string;

    public function setImage(string $Image): BannerInterface;

    public function getImageAlt(): string;

    public function setImageAlt(string $imageAlt): BannerInterface;

    public function getTargetUrl(): string;

    public function setTargetUrl(string $targetUrl): BannerInterface;

    public function getHoverText(): string;

    public function setHoverText(string $hoverText): BannerInterface;

    public function getStoreId(): int;

    public function setStoreId(int $storeId): BannerInterface;

    public function getVisibleOn(): int;

    public function setVisibleOn(int $visible): BannerInterface;

    public function getTargetType(): string;

    public function setTargetType(string $type): BannerInterface;

    public function getStartDate(): string;

    public function setStartDate(string $date): void;

    public function getEndDate(): string;

    public function setEndDate(string $date): void;
}
