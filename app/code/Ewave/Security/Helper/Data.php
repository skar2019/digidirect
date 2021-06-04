<?php
namespace Ewave\Security\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * Configuration path to enable access settings for admin area
     */
    const XML_PATH_SECURITY_ACCESS_ENABLED = 'security/access/enabled';

    /**
     * Configuration path to ips settings
     */
    const XML_PATH_SECURITY_ACCESS_IPS = 'security/access/ips';

    /**
     * Enable Access
     * @return bool
     */
    public function isAccessEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SECURITY_ACCESS_ENABLED
        ) && !empty($this->getIpsToArray());
    }

    /**
     * Retrieve IPs
     * @return string
     */
    protected function _getIps()
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SECURITY_ACCESS_IPS
        );
    }

    /**
     * Retrieve array IPs
     * @return array
     */
    public function getIpsToArray()
    {
        $ips = $this->_getIps();
        if (empty($ips)) {
            return [];
        }
        return explode(',', $ips);
    }
}
