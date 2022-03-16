<?php

namespace Marketplacer\Brand\Api\Data;

use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use Marketplacer\Brand\Model\Brand;
use Marketplacer\BrandApi\Api\Data\MarketplacerBrandInterface;

/**
 * @method hasStoreId() bool
 */
interface BrandInterface extends MarketplacerBrandInterface
{
    public const ROW_ID = 'row_id';
//  public const BRAND_ID = 'brand_id';                     //part of parent MarketplacerBrandInterface
    public const STORE_ID = 'store_id';
    public const OPTION_ID = 'option_id';
    public const STATUS = 'status';
    public const SORT_ORDER = 'sort_order';
//  public const NAME = 'name';                             //part of parent MarketplacerBrandInterface
//  public const LOGO = 'logo';                             //part of parent MarketplacerBrandInterface
    public const URL_KEY = 'url_key';
    public const META_TITLE = 'meta_title';
    public const META_DESCRIPTION = 'meta_description';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const STATUS_ENABLED = '1';
    public const STATUS_DISABLED = '0';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getRowId();

    /**
     * @param int $rowId
     * @return BrandInterface
     */
    public function setRowId($rowId);

    /**
     * Get Brand Store ID
     *
     * @return int|null
     */
    public function getStoreId();

    /**
     * @param int $storeId
     * @return BrandInterface
     */
    public function setStoreId($storeId);

    /**
     * Get Attribute option ID
     *
     * @return int|null
     */
    public function getOptionId();

    /**
     * @param int $optionId
     * @return BrandInterface
     */
    public function setOptionId($optionId);

    /**
     * Get Status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * @param int $status
     * @return BrandInterface
     */
    public function setStatus($status);

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * Get Brand Sort order
     *
     * @return int|null
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     * @return BrandInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * @return string
     */
    public function getUrlKey();

    /**
     * @param string $urlKey
     * @return BrandInterface
     */
    public function setUrlKey($urlKey);

    /**
     * @return string
     */
    public function getMetaTitle();

    /**
     * @param string $metaTitle
     * @return BrandInterface
     */
    public function setMetaTitle($metaTitle);

    /**
     * @return string
     */
    public function getMetaDescription();

    /**
     * @param string $metaDescription
     * @return BrandInterface
     */
    public function setMetaDescription($metaDescription);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return BrandInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     * @return BrandInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return AttributeOptionInterface
     * @throws NoSuchEntityException
     */
    public function getAttributeOption();

    /**
     * @return bool
     */
    public function hasAttributeOption();

    /**
     * @param AttributeOptionInterface $attributeOption
     * @return $this
     */
    public function setAttributeOption(AttributeOptionInterface $attributeOption);

    /**
     * Clean url key
     * @param string $urlKey
     * @return string
     * @throws ValidatorException
     */
    public function getSanitizedUrlKey($urlKey);

    /**
     * Validate url key
     * @param string $urlKey
     * @return true If url key is valid
     * @throws ValidatorException
     */
    public function validateUrlKey($urlKey);

    /**
     * Process url rewrites
     *
     * @return $this
     */
    public function processUrlRewrites();

    /**
     * Delete url rewrites
     *
     * @return $this
     */
    public function deleteUrlRewrites();

    /**
     * @return Brand
     * @throws ValidatorException
     */
    public function refreshUrlKey();
}
