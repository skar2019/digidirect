<?php
namespace Digidirect\TradeUp\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'digidirect_tradeup/general/enabled';
    const XML_PATH_RECIPIENT_EMAIL = 'digidirect_tradeup/general/recipient_email';

    /**
     * Check if Trade Up functionality is enabled
     *
     * @return bool
     */
    public function isTradeUPFromEnabled()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );

        return $value === null ? true : (bool) $value;
    }

    /**
     * Get recipient email
     *
     * @return string
     */
    public function getRecipientEmail()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RECIPIENT_EMAIL,
            ScopeInterface::SCOPE_STORE
        );
    }
}
