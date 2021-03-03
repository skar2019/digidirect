<?php

namespace Digidirect\Navigation\Model\ResourceModel\Type;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'type_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Digidirect\Navigation\Model\Type', 'Digidirect\Navigation\Model\ResourceModel\Type');
    }

    /**
     * Return types
     *
     * @return []
     */
    public function getTypesArray()
    {
        return $this->_toOptionArray('type_id', 'type_name');
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    protected function _beforeLoad()
    {
        $this->setOrder('menu_type_code', self::SORT_ORDER_ASC);
        return parent::_beforeLoad();
    }
}
