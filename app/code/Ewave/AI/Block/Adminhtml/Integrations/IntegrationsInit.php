<?php
namespace Ewave\AI\Block\Adminhtml\Integrations;

/**
 * Class IntegrationsInit
 * @package Ewave\AI\Block\Adminhtml\Integrations
 */
class IntegrationsInit extends \Magento\Backend\Block\Template
{
    /**
     * Init constructor
     * @return void
     */
    public function _construct()
    {
        $this->setTemplate('integrations/integrations_init.phtml');
        parent::_construct();
    }

    /**
     * @return string
     */
    public function getRunOptionsUrl()
    {
        return $this->getUrl('ewave_ai/integration/runOptions');
    }

    /**
     * @return string
     */
    public function getRunUrl()
    {
        return $this->getUrl('ewave_ai/integration/run');
    }

    /**
     * @return string
     */
    public function getDeleteScheduleUrl()
    {
        return $this->getUrl('ewave_ai/integration/deleteSchedule');
    }

    /**
     * @return string
     */
    public function getLogDataUrl()
    {
        return $this->getUrl('ewave_ai/integration/runLogAjaxData');
    }
}
