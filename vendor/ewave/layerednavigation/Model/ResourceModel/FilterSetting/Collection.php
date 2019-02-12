<?php
namespace Ewave\LayeredNavigation\Model\ResourceModel\FilterSetting;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Ewave\LayeredNavigation\Model\FilterSetting',
            'Ewave\LayeredNavigation\Model\ResourceModel\FilterSetting'
        );
    }
}
