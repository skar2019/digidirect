<?php
namespace Digidirect\Cform\Model\ResourceModel\Cform;
 
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection
{
 
    protected $_idFieldName = \Digidirect\Cform\Model\Cform::CONTECT_ID;
     
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Digidirect\Cform\Model\Cform', 'Digidirect\Cform\Model\ResourceModel\Cform');
    }
 
}