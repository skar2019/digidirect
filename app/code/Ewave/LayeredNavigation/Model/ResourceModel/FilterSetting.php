<?php
namespace Ewave\LayeredNavigation\Model\ResourceModel;

class FilterSetting extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ewave_layerednavigation_filter_setting', 'setting_id');
    }
}
