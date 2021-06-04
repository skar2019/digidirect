<?php
namespace Ewave\ProductPriority\Helper;

/**
 * Class Config
 * @package Ewave\ProductPriority\Helper
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * XML path to config settings
     */
    const XML_PRODUCT_PRIORITY_SORT_SETTINGS = 'product_priority/sort_settings/';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PRODUCT_PRIORITY_SORT_SETTINGS . 'is_active');
    }

    /**
     * @return string
     */
    public function getSortBy()
    {
        return $this->scopeConfig->getValue(self::XML_PRODUCT_PRIORITY_SORT_SETTINGS . 'sort_by');
    }

    /**
     * @return string
     */
    public function getSortByPeriod()
    {
        return $this->scopeConfig->getValue(self::XML_PRODUCT_PRIORITY_SORT_SETTINGS . 'sort_by_period');
    }

    /**
     * @return bool
     */
    public function isPushOutOfStockInBottom()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PRODUCT_PRIORITY_SORT_SETTINGS . 'push_out_of_stock_bottom');
    }

    /**
     * @return bool
     */
    public function isPushNewProductToTop()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PRODUCT_PRIORITY_SORT_SETTINGS . 'push_new_product_to_the_top');
    }

    /**
     * @return string
     */
    public function getPeriodForNewProducts()
    {
        return $this->scopeConfig->getValue(self::XML_PRODUCT_PRIORITY_SORT_SETTINGS . 'period_for_new_products');
    }
}
