<?php

namespace Ewave\Faq\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const FAQ_URL = 'ewave_faq/general/faq_page_url';
    const FAQ_SUFFIX = 'ewave_faq/general/main_url_suffix';
    const FAQ_CATEGORY_SUFFIX = 'ewave_faq/general/category_url_suffix';
    const FAQ_ENABLED = 'ewave_faq/general/faq_enabled';
    const FAQ_PERPAGE = 'ewave_faq/general/faq_per_page';
    const FAQ_TAG_ENABLE = 'ewave_faq/general/faq_tag_enable';
    const FAQ_SEARCH_ENABLE = 'ewave_faq/general/faq_search_enable';
    const DEFAULT_PER_PAGE_COUNT = 20;

    /**
     * @var null|string
     */
    private $faqUrl = null;

    /**
     * @return bool
     */
    public function isModuleEnabled()
    {
        return $this->scopeConfig->getValue(self::FAQ_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isTagsEnabled()
    {
        return $this->scopeConfig->getValue(self::FAQ_TAG_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isSearchEnabled()
    {
        return $this->scopeConfig->getValue(self::FAQ_SEARCH_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve FAQ page url
     *
     * @return mixed
     */
    public function getFaqUrl()
    {
        if (null === $this->faqUrl) {
        }
        $faqUrl = $this->scopeConfig->getValue(self::FAQ_URL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $faqUrl = \str_replace($this->getFaqSuffix(), '', $faqUrl);
        $faqUrl = $faqUrl . $this->getFaqSuffix();
        return $faqUrl;
    }

    /**
     * @param string $url
     * @return bool
     */
    public function isValidUrl($url)
    {
        return $this->getFaqUrl() == $url;
    }

    /**
     * @return string
     */
    public function getAjaxCallUrl()
    {
        return 'faq/index/ajaxview';
    }

    /**
     * @return mixed
     */
    public function getQuestionsPerPage()
    {
        $perPage = $this->scopeConfig->getValue(self::FAQ_PERPAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$perPage || $perPage <= 0) {
            $perPage = self::DEFAULT_PER_PAGE_COUNT;
        }

        return $perPage;
    }

    /**
     *  Check whether page identifier is valid
     *
     * @param string $identifier
     * @return bool
     */
    public function isValidPageIdentifier($identifier)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $identifier);
    }

    /**
     * Check whether page identifier is numeric
     *
     * @param string $identifier
     * @return bool
     */
    public function isNumericPageIdentifier($identifier)
    {
        return preg_match('/^[0-9]+$/', $identifier);
    }

    /**
     * @return bool
     */
    public function isAjaxEnabled()
    {
        return $this->scopeConfig->isSetFlag('ewave_faq/general/ajax_category', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getFaqSuffix(): string
    {
        return (string)trim($this->scopeConfig->getValue(static::FAQ_SUFFIX));
    }

    /**
     * @return string
     */
    public function getPageUrlWithoutSuffix(): string
    {
        $pageUrl = $this->getFaqUrl();
        $suffix = $this->getFaqSuffix();
        return \str_replace($suffix, '', $pageUrl);
    }

    /**
     * @return string
     */
    public function getCategoryUrlSuffix(): string
    {
        return (string)$this->scopeConfig->getValue(static::FAQ_CATEGORY_SUFFIX);
    }

    /**
     * @param string $url
     * @return string
     */
    public function removeFaqCategorySuffix(string $url): string
    {
        $suffix = $this->getCategoryUrlSuffix();
        return \str_replace($suffix, '', $url);
    }
}
