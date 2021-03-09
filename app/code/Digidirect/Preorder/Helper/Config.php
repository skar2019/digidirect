<?php

namespace Digidirect\PreOrder\Helper;

/**
 * Class Config
 *
 * @package Digidirect\PreOrder\Helper
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const EWAVE_PREORDER = 'digidirect_preorder';

    const XML_PATH_SECTION_GENERAL = self::EWAVE_PREORDER . '/general/';
    const XML_PATH_ENABLED = self::XML_PATH_SECTION_GENERAL . 'enabled';
    const XML_PATH_ALLOW_EMPTY_QTY = self::XML_PATH_SECTION_GENERAL . 'allowemptyqty';
    const XML_PATH_DISABLE_FOR_POSITIVE_QTY = self::XML_PATH_SECTION_GENERAL . 'disableforpositiveqty';

    const XML_PATH_SECTION_DISPLAY = self::EWAVE_PREORDER . '/display/';
    const XML_PATH_ADD_TO_CART_BUTTON_TEXT = self::XML_PATH_SECTION_DISPLAY . 'addtocartbuttontext';
    const XML_PATH_DEFAULT_PREORDER_NOTE = self::XML_PATH_SECTION_DISPLAY . 'defaultpreordernote';
    const XML_PATH_ORDER_PREORDER_WARNING = self::XML_PATH_SECTION_DISPLAY . 'orderpreorderwarning';

    const XML_PATH_SECTION_ADDITIONAL = self::EWAVE_PREORDER . '/additional/';
    const XML_PATH_DISCOVER_COMPOSITE_OPTIONS = self::XML_PATH_SECTION_ADDITIONAL . 'discovercompositeoptions';
    const XML_PATH_BACKORDERS_FOR_AVAILABILITY_DATE
        = self::XML_PATH_SECTION_ADDITIONAL.'backordersforavailabilitydate';

    /**
     * Is preorder enabled
     *
     * @return bool
     */
    public function preordersEnabled()
    {
        return $this->getCurrentStoreConfig(self::XML_PATH_ENABLED, true);
    }

    /**
     * Get default preorder note
     *
     * @return string
     */
    public function getDefaultPreorderNote()
    {
        return $this->getCurrentStoreConfig(self::XML_PATH_DEFAULT_PREORDER_NOTE);
    }

    /**
     * Get default preorder cart label
     *
     * @return string
     */
    public function getDefaultPreorderCartLabel()
    {
        return $this->getCurrentStoreConfig(self::XML_PATH_ADD_TO_CART_BUTTON_TEXT);
    }

    /**
     * Get default preorder cart label
     *
     * @return string
     */
    public function getOrderPreorderWarning()
    {
        return $this->getCurrentStoreConfig(self::XML_PATH_ORDER_PREORDER_WARNING);
    }

    /**
     * Whether discover composite options is enabled
     *
     * @return bool
     */
    public function isDiscoverCompositeOptionsEnabled()
    {
        return $this->getCurrentStoreConfig(self::XML_PATH_DISCOVER_COMPOSITE_OPTIONS, true);
    }

    /**
     * Whether empty qty is allow
     *
     * @return bool
     */
    public function isAllowEmptyQty()
    {
        return $this->getCurrentStoreConfig(self::XML_PATH_ALLOW_EMPTY_QTY, true);
    }

    /**
     * Is disable for positive qty
     *
     * @return bool
     */
    public function disableForPositiveQty()
    {
        return $this->getCurrentStoreConfig(self::XML_PATH_ALLOW_EMPTY_QTY, true)
               && $this->getCurrentStoreConfig(self::XML_PATH_DISABLE_FOR_POSITIVE_QTY, true);
    }

    /**
     * Get current store config
     *
     * @param string $path
     * @param bool $flag
     * @return string|bool
     */
    protected function getCurrentStoreConfig(string $path, bool $flag = false)
    {
        $method = $flag ? 'isSetFlag' : 'getValue';
        $result = $this->scopeConfig->$method(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $result;
    }

    /**
     * Get backorders for availability date config
     *
     * @return int
     */
    public function getBackordersForAvailabilityDateConfig()
    {
        return $this->getCurrentStoreConfig(self::XML_PATH_BACKORDERS_FOR_AVAILABILITY_DATE);
    }
}
