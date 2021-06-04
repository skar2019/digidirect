<?php

namespace Ewave\CriteoOneTag\Helper;

/**
 * Class Data
 *
 * @package Ewave\CriteoOneTag\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const GOOGLE_ACTIVE_PATCH = 'google/analytics/active';

    /**
     * @param int $scopeCode
     * @return bool
     */
    public function isEnable($scopeCode = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::GOOGLE_ACTIVE_PATCH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }
}
