<?php


namespace Digidirect\PaSalesForceProductRecommendation\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Tarun\StackOverflow\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Config path to get setting for whether to show popup on each login
     */
    const XML_CONFIG_PATH = 'login_popup/general/show_on_every_login';

    /**
     * @return mixed
     */
    public function getShowPopupOnEachLoginConfig()
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_PATH,ScopeInterface::SCOPE_STORE);
    }
}