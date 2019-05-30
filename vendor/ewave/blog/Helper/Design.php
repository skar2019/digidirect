<?php

namespace Ewave\Blog\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * Class Design
 */
class Design extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PATH_XML_DESIGN_THEME = 'ewave_blog/design/theme';
    const PATH_XML_DESIGN_UPDATE_XML_DATA = 'ewave_blog/design/update_xml';

    /**
     * @return mixed
     */
    public function getSpecialTheme()
    {
        return $this->scopeConfig->getValue(self::PATH_XML_DESIGN_THEME, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function getXmlUpdates()
    {
        return $this->scopeConfig->getValue(
            self::PATH_XML_DESIGN_UPDATE_XML_DATA,
            ScopeInterface::SCOPE_STORE
        );
    }
}
