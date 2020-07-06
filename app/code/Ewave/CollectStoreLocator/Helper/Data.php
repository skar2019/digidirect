<?php
namespace Ewave\CollectStoreLocator\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class Data
 * @package Ewave\CollectStoreLocator\Controller\Ajax
 */
class Data extends AbstractHelper
{
    /**
     * @return string
     */
    public function getAjaxLocatorBlockUrl()
    {
        return $this->_getUrl('ewavecollectlocator/ajax_locator/block');
    }
}
