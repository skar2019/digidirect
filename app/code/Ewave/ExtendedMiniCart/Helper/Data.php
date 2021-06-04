<?php

namespace Ewave\ExtendedMiniCart\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 *
 * @package Ewave\ExtendedMiniCart\Helper
 */
class Data extends AbstractHelper
{
    const MODULE = 'ewave_extendedminicart';

    const XML_CONFIG_GENERAL_SHOW_LINE_ITEM_SUBTOTAL = self::MODULE . '/general/show_line_item_subtotal';

    /**
     * @param null $store
     * @param string $scope
     * @return bool
     */
    public function isShowLineItemSubtotal($store = null, $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_GENERAL_SHOW_LINE_ITEM_SUBTOTAL, $scope, $store);
    }
}
