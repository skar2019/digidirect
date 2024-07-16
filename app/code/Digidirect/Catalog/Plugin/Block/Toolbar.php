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
        $collection->getSelect()->order('created_at DESC');
        return $proceed($collection);
    }

}