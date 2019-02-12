<?php
namespace Ewave\SocialSharing\Helper\Config;

use \Magento\Store\Model\ScopeInterface;

class Pinterest extends \Ewave\SocialSharing\Helper\Config
{
    const SOCIAL_SHARING_PIN = 'ewave_social_sharing/sharing_pin/';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::SOCIAL_SHARING_PIN . 'enable',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isPinDisplayCountSettings()
    {
        return $this->scopeConfig->isSetFlag(
            self::SOCIAL_SHARING_PIN . 'count',
            ScopeInterface::SCOPE_STORE
        );
    }
}
