<?php

namespace Ewave\StyleGuide\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_SHOW = 'dev/styleguide/show';

    public function getShowMode()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SHOW, ScopeInterface::SCOPE_STORE);
    }
}
