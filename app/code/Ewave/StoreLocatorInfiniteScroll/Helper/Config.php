<?php

namespace Ewave\StoreLocatorInfiniteScroll\Helper;

use Ewave\InfiniteScroll\Helper\Data as InfiniteScrollHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends InfiniteScrollHelper
{
    const XML_PATH_ENABLED = 'ewave_infinitescroll_config/store_locator_listing_page/enable';
    const XML_PATH_ACTION = 'ewave_infinitescroll_config/store_locator_listing_page/action';
    const XML_PATH_LIMIT = 'ewave_infinitescroll_config/store_locator_listing_page/limit';

    /**
     * Is Infinite Scroll Enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return parent::isEnabled() && $this->scopeConfig->isSetFlag(
                self::XML_PATH_ENABLED,
                ScopeInterface::SCOPE_STORE
            );
    }

    /**
     * Get Action Type
     *
     * @return int
     */
    public function getActionType()
    {
        return $this->_getActionType(self::XML_PATH_ACTION);
    }

    /**
     * Get Limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LIMIT,
            ScopeInterface::SCOPE_STORE
        ) ?: parent::getLimit();
    }
}
