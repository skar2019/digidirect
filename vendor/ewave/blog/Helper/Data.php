<?php

namespace Ewave\Blog\Helper;

use Magento\Framework\DataObject;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const GENERAL_SETTINGS = 'ewave_blog/general/';

    const DISPLAY_SETTINGS = 'ewave_blog/display_settings/';

    const RELATED_SETTINGS = 'ewave_blog/related_setting/';

    const COMMENT_SETTINGS = 'ewave_blog/comments/';

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->getGeneralSettingsConfig('active') && $this->isModuleOutputEnabled('Ewave_Blog');
    }

    /**
     * @param string $storePath
     * @return mixed
     */
    public function getStoreConfig($storePath)
    {
        return $this->scopeConfig->getValue($storePath, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param string $setting
     * @param string $scopeType
     * @param null|int $scopeCode
     * @return string
     */
    public function getGeneralSettingsConfig(
        $setting,
        $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        return $this->scopeConfig->getValue(self::GENERAL_SETTINGS . $setting, $scopeType, $scopeCode);
    }

    /**
     * @param string $setting
     * @param string $scopeType
     * @param null|int $scopeCode
     * @return string
     */
    public function getRelatedSettingsConfig(
        $setting,
        $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        return $this->scopeConfig->getValue(self::RELATED_SETTINGS . $setting, $scopeType, $scopeCode);
    }

    /**
     * @param string $setting
     * @param string $scopeType
     * @param null|int $scopeCode
     * @return string
     */
    public function getDisplaySettingsConfig(
        $setting,
        $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        return $this->scopeConfig->getValue(self::DISPLAY_SETTINGS . $setting, $scopeType, $scopeCode);
    }

    /**
     * @return number
     */
    public function getPostPerPage()
    {
        return abs((int)$this->getGeneralSettingsConfig('postonlist'));
    }

    /**
     * @param string $setting
     * @param string $scopeType
     * @param null|int $scopeCode
     * @return string
     */
    public function getCommentSettingsConfig(
        $setting,
        $scopeType = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ) {
        return $this->scopeConfig->getValue(self::COMMENT_SETTINGS . $setting, $scopeType, $scopeCode);
    }

    /**
     * It does not make any sense to localize hardcoded date but for legacy this method is introduced
     *
     * @return bool
     * @since 2.0.1
     */
    public function isDateLocalizationRequired(): bool
    {
        return $this->scopeConfig->isSetFlag('dev/blog/localize_publish_date', ScopeInterface::SCOPE_STORE);
    }
}
