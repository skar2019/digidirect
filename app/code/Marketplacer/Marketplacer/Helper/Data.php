<?php

namespace Marketplacer\Marketplacer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_DISPLAY_SELLER_BUSINESS_NUMBER = 'marketplacer_sales_displaying/seller/display_business_number';
    const XML_PATH_TITLE_FOR_SELLER_BUSINESS_NUMBER = 'marketplacer_sales_displaying/seller/title_for_business_number';
    const XML_PATH_DISPLAY_BRAND_NAME = 'marketplacer_sales_displaying/brand/display_brand_name';

    /**
     * @param mixed $storeId
     * @return bool
     */
    public function displaySellerBusinessNumber($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DISPLAY_SELLER_BUSINESS_NUMBER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param mixed $storeId
     * @return mixed
     */
    public function getTitleForSellerBusinessNumber($storeId = null)
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_TITLE_FOR_SELLER_BUSINESS_NUMBER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return trim($value) ?? 'Business Number';
    }

    /**
     * @param mixed $storeId
     * @return bool
     */
    public function displayBrandName($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DISPLAY_BRAND_NAME, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
