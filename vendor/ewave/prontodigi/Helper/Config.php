<?php

namespace Ewave\ProntoDigi\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const PRODUCTS_DISABLED_PERCENT = 'ewave_pronto/api_products/disabled_products_percent_skip_update';
    const INVENTORY_PRODUCTS_DISABLED_PERCENT = 'ewave_pronto/api_inventory/disabled_products_percent_skip_update';

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
}
