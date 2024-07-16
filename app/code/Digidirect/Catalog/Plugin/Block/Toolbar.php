<?php

namespace Digidirect\Catalog\Plugin\Block;

use Magento\Store\Model\StoreManagerInterface;

class Toolbar
{
    protected $_storeManager;

    public function __construct (
        StoreManagerInterface $storeManager
    ){
        $this->_storeManager = $storeManager;
    }

    public function aroundSetCollection(Productdata $subject, \Closure $proceed, $collection) {
        $collection->getSelect()->joinLeft( 
            'sales_order_item', 
            'e.entity_id = sales_order_item.product_id', 
            array('qty_ordered'=>'SUM(sales_order_item.qty_ordered)')) 
            ->group('e.entity_id') 
            ->order('qty_ordered DESC');
        return $proceed($collection);
    }

}