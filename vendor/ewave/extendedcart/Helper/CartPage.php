<?php

namespace Ewave\ExtendedCart\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class CartPage extends AbstractHelper
{
    const SECTION_XML_PATH = 'ewave_extendedcart/cart_page/';

    const XML_PATH_USE_AJAX_FOR_CART_UPDATE = self::SECTION_XML_PATH . 'use_ajax_for_cart_update';

    /**
     * @return bool
     */
    public function isAjaxUpdateEnabled()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_USE_AJAX_FOR_CART_UPDATE, ScopeInterface::SCOPE_STORE);
    }
}
