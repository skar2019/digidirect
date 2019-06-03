<?php

namespace Ewave\ProntoDigi\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const TIMEZONE = 'ewave_pronto/api/timezone';
    const PRODUCTS_DISABLED_PERCENT = 'ewave_pronto/api_products/disabled_products_percent_skip_update';
    const INVENTORY_PRODUCTS_DISABLED_PERCENT = 'ewave_pronto/api_inventory/disabled_products_percent_skip_update';
    const INVENTORY_DIFF_LAST_MINUTES = 'ewave_pronto/api_inventory/diff_last_minutes';
    const INVENTORY_DECREASE_QUANTITY_FOR_ALL_SOURCES = 'ewave_pronto/api_inventory/decrease_quantity_for_all_sources';

    /**
     * @return string
     */
    public function getTimezone()
    {
        return (string)$this->scopeConfig->getValue(self::TIMEZONE);
    }

    /**
     * @return int
     */
    public function getProductsDisabledPercent()
    {
        return (int)$this->scopeConfig->getValue(self::PRODUCTS_DISABLED_PERCENT);
    }

    /**
     * @return int
     */
    public function getInventoryProductsDisabledPercent()
    {
        return (int)$this->scopeConfig->getValue(self::INVENTORY_PRODUCTS_DISABLED_PERCENT);
    }

    /**
     * @return int
     */
    public function getInventoryDiffLastMinutes()
    {
        return (int)$this->scopeConfig->getValue(self::INVENTORY_DIFF_LAST_MINUTES);
    }

    /**
     * @return int
     */
    public function getInventoryDecreaseQuantityForAllSources()
    {
        return (int)$this->scopeConfig->getValue(self::INVENTORY_DECREASE_QUANTITY_FOR_ALL_SOURCES);
    }
}
