<?php
namespace Digidirect\DigiMarketSeller\Model\ResourceModel\DigiMarketSeller;
 
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection
{
 
    protected $_idFieldName = \Digidirect\DigiMarketSeller\Model\DigiMarketSeller::CONTECT_ID;
     
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Digidirect\DigiMarketSeller\Model\DigiMarketSeller', 'Digidirect\DigiMarketSeller\Model\ResourceModel\DigiMarketSeller');
    }
 
}