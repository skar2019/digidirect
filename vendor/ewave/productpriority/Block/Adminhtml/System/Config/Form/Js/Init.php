<?php
namespace Ewave\ProductPriority\Block\Adminhtml\System\Config\Form\Js;

/**
 * Class Init
 * @package Ewave\ProductPriority\Block\Adminhtml\System\Config\Form\Js
 */
class Init extends \Magento\Backend\Block\Template
{
    /**
     * @return string
     */
    public function getCalculateUrl()
    {
        return $this->getUrl('ewave_productpriority/index/index');
    }
}
