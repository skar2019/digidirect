<?php

namespace Ewave\OrderImages\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 */
class Data extends AbstractHelper
{
    const XML_PATH_ENABLED = 'product_images/order_detail/enable';

    /**
     * @return bool
     */
    public function isModuleOrderImagesEnable()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
    }
}
