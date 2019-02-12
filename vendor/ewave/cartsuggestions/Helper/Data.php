<?php
namespace Ewave\CartSuggestions\Helper;

/**
 * Class Data
 * @package Ewave\CartSuggestions\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CART_SUGGESTION_ENABLED   = 'ewave_cartsuggestions/general/cartsuggestions_enabled';
    const CART_SUGGESTION_PRODUCT_NUMBER = 'ewave_cartsuggestions/general/product_number';
    const CART_SUGGESTION_LINK_TEXT = 'ewave_cartsuggestions/general/suggestions_link';
    const DEFAULT_PRODUCT_NUMBER = 5;

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::CART_SUGGESTION_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve product number
     * @return int
     */
    public function getProductNumber()
    {
        $productNumber = $this->scopeConfig->getValue(
            self::CART_SUGGESTION_PRODUCT_NUMBER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($productNumber === null) {
            $productNumber = self::DEFAULT_PRODUCT_NUMBER;
        }

        return $productNumber;
    }

    /**
     * Retrieve suggestions link text
     * @return mixed
     */
    public function getSuggestionsLinkText()
    {
        return $this->scopeConfig->getValue(
            self::CART_SUGGESTION_LINK_TEXT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
