<?php

namespace Ewave\ExtendedCart\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class AddToCartConfirmationPopup extends AbstractHelper
{
    const SECTION_XML_PATH = 'ewave_extendedcart/add_to_cart_confirmation_popup/';

    const XML_PATH_ENABLED = self::SECTION_XML_PATH . 'enabled';
    const XML_PATH_PROMOTION_BLOCK = self::SECTION_XML_PATH . 'promotion_block';
    const XML_PATH_PROMOTION_BLOCK_PRODUCTS_COUNT = self::SECTION_XML_PATH . 'promotion_block_products_count';

    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getPromotionBlock()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PROMOTION_BLOCK, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function getPromotionBlockProductsCount()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_PROMOTION_BLOCK_PRODUCTS_COUNT,
            ScopeInterface::SCOPE_STORE
        );
    }
}
