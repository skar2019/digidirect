<?php

namespace Digidirect\MyOrderItemsGroups\Helper;

use Magento\Store\Model\ScopeInterface;
use Digidirect\MyOrderItems\Helper\Config as ConfigHelper;

/**
 * Class Config
 * @package Digidirect\MyOrderItemsGroups\Helper
 */
class Config extends ConfigHelper
{
    /**
     * Constant mapping path
     */
    const XML_PATH_DEFAULT_GROUP_NAME = 'digidirect_myorderitems/groups/default_group_name';

    /**
     * @return mixed
     */
    public function getDefaultGroupName()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_GROUP_NAME,
            ScopeInterface::SCOPE_STORE
        );
    }
}
