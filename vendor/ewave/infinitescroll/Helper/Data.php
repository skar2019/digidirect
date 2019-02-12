<?php
namespace Ewave\InfiniteScroll\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Ewave\InfiniteScroll\Model\Config\Source\Action;

class Data extends AbstractHelper
{
    const PARAM_NAME = '_is';
    
    const XML_PATH_ENABLED = 'ewave_infinitescroll_config/general/enabled';
    
    const XML_PATH_LIMIT = 'ewave_infinitescroll_config/general/limit';

    const XML_PATH_CATALOG_PROCESSOR_ENABLED = 'ewave_infinitescroll_config/catalog/enabled';
    const XML_PATH_CATALOG_PROCESSOR_ACTION = 'ewave_infinitescroll_config/catalog/action';
    const XML_PATH_CATALOG_PROCESSOR_LIMIT = 'ewave_infinitescroll_config/catalog/limit';
    
    const XML_PATH_SEARCH_PROCESSOR_ENABLED = 'ewave_infinitescroll_config/search/enabled';
    const XML_PATH_SEARCH_PROCESSOR_ACTION = 'ewave_infinitescroll_config/search/action';
    const XML_PATH_SEARCH_PROCESSOR_LIMIT = 'ewave_infinitescroll_config/search/limit';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isCatalogProcessorEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CATALOG_PROCESSOR_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LIMIT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isSearchProcessorEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SEARCH_PROCESSOR_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return int
     */
    public function getCatalogActionType()
    {
        return $this->_getActionType(self::XML_PATH_CATALOG_PROCESSOR_ACTION);
    }

    /**
     * @return int
     */
    public function getSearchActionType()
    {
        return $this->_getActionType(self::XML_PATH_SEARCH_PROCESSOR_ACTION);
    }

    /**
     * @return int
     */
    public function getCatalogLimit()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATALOG_PROCESSOR_LIMIT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) ?: $this->getLimit();
    }

    /**
     * @return int
     */
    public function getSearchLimit()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SEARCH_PROCESSOR_LIMIT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) ?: $this->getLimit();
    }

    /**
     * @param string $path
     * @return mixed|string
     */
    protected function _getActionType($path)
    {
        $action = $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (in_array($action, [Action::LOAD_ACTION_CLICK, Action::LOAD_ACTION_SCROLL])) {
            return $action;
        }
        return Action::LOAD_ACTION_CLICK;
    }
}
