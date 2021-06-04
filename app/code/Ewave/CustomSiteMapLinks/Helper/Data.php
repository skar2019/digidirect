<?php
namespace Ewave\CustomSitemapLinks\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_CUSTOM_LINKS_CHANGEFREQ = 'ewave_customsitemaplinks/custom/changefreq';

    const XML_PATH_CUSTOM_LINKS_PRIORITY = 'ewave_customsitemaplinks/custom/priority';

    const XML_PATH_CUSTOM_LINKS = 'ewave_customsitemaplinks/custom/links';

    /**
     * Get custom links change frequency
     *
     * @param int $storeId
     * @return string
     */
    public function getCustomLinksChangefreq($storeId)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_CUSTOM_LINKS_CHANGEFREQ,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get custom links priority
     *
     * @param int $storeId
     * @return string
     */
    public function getCustomLinksPriority($storeId)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_CUSTOM_LINKS_PRIORITY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get custom links
     *
     * @param int $storeId
     * @return array
     */
    public function getCustomLinks($storeId)
    {
        $links = $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOM_LINKS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $links = explode(chr(10), $links);
        return array_map('trim', array_filter($links));
    }
}
