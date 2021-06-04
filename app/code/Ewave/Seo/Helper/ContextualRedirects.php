<?php

namespace Ewave\SEO\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class ContextualRedirects
 *
 * @package Ewave\SEO\Helper
 */
class ContextualRedirects extends AbstractHelper
{
    const XML_PATH_CONTEXTUAL_REDIRECTS_ENABLED = 'ewave_seo/general/enable_contextual_redirects';

    /**
     * Check if trailing slash enabled
     *
     * @return bool
     */
    public function isContextualRedirectsEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_CONTEXTUAL_REDIRECTS_ENABLED);
    }
}
