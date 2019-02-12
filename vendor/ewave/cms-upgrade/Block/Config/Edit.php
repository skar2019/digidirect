<?php

namespace Ewave\CmsUpgrade\Block\Config;

use Ewave\CmsUpgrade\Helper\Data;

/**
 * Class Edit
 * @package Ewave\CmsUpgrade\Block\Config
 */
class Edit extends \Magento\Config\Block\System\Config\Edit
{
    /**
     * @var Data
     */
    protected $_helper;

    /**
     * Edit constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Config\Model\Config\Structure $configStructure,
        Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $configStructure, $data);
    }

    /**
     * Prepare layout object
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        if ($this->_helper->isModuleOutputEnabled()
        ) {
            $this->getToolbar()->addChild(
                'generate_upgrade_script_button',
                'Magento\Backend\Block\Widget\Button',
                [
                    'id' => 'generate_upgrade_script_button',
                    'label' => __('Generate Upgrade Script'),
                    'class' => 'save primary',
                    'on_click' => "if(confirm('Are you sure you want to generate Upgrade Script?'))
                {jQuery('#config-edit-form').attr('action', '" .
                        $this->escapeXssInUrl($this->_getGenerateUrl()) . "'); 
                jQuery('#config-edit-form').submit()}",
                ]
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    protected function _getGenerateUrl()
    {
        $section = $this->getRequest()->get('section');
        $website = $this->getRequest()->get('website');
        $store = $this->getRequest()->get('store');
        $params = ['section' => $section];
        if ($website) {
            $params['website'] = $website;
        }
        if ($store) {
            $params['store'] = $store;
        }
        return $this->getUrl('ewavecmsupgrade/config/generate', $params);
    }
}
