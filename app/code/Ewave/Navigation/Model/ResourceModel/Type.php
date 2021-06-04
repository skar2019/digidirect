<?php

namespace Ewave\Navigation\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Type extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ewave_navigation_menu_item_type', 'type_id');
    }
}
