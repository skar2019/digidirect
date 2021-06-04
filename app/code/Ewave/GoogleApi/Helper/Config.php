<?php
namespace Ewave\Googleapi\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @package Ewave\Googleapi\Helper
 */
class Config extends AbstractHelper
{
    const XML_PATH_GOOGLE_API_KEY = 'ewave_googleapi_config/general/google_api_key';

    /**
     * @return mixed
     */
    public function getGoogleApiKey()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_GOOGLE_API_KEY, ScopeInterface::SCOPE_STORE);
    }
}