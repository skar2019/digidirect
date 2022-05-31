<?php

namespace Marketplacer\Brand\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    public const XML_PATH_GENERAL_ENABLED = 'marketplacer_brand/general/enabled';
    public const XML_PATH_GENERAL_ADMIN_EDIT_ALLOWED = 'marketplacer_brand/general/allow_admin_edit';

    public const XML_PATH_SEO_BASE_URL_KEY = 'marketplacer_brand/seo/base_url_key';
    public const XML_PATH_SEO_URL_SUFFIX = 'marketplacer_brand/seo/url_suffix';

    public const XML_PATH_LISTING_PAGE_TITLE = 'marketplacer_brand/listing/page_title';
    public const XML_PATH_LISTING_META_TITLE = 'marketplacer_brand/listing/meta_title';
    public const XML_PATH_LISTING_META_DESCRIPTION = 'marketplacer_brand/listing/meta_description';

    /**
     * @param string | int | null $storeId
     * @return bool
     */
    public function isEnabledOnStorefront($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string | int | null $storeId
     * @return bool
     */
    public function isAdminEditAllowed($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GENERAL_ADMIN_EDIT_ALLOWED,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $storeId
        );
    }

    /**
     * @param string | int | null $storeId
     * @return mixed
     */
    public function getBaseUrlKey($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_BASE_URL_KEY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string | int | null $storeId
     * @return mixed
     */
    public function getUrlSuffix($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEO_URL_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string | int | null $storeId
     * @return mixed
     */
    public function getListingPageTitle($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LISTING_PAGE_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string | int | null $storeId
     * @return mixed
     */
    public function getListingMetaTitle($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LISTING_META_TITLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string | int | null $storeId
     * @return mixed
     */
    public function getListingMetaDescription($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LISTING_META_DESCRIPTION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
