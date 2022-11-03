<?php

namespace Digidirect\Cform\Model;

class Cform extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    
    protected function _construct()
    {
        $this->_init('Digidirect\Cform\Model\ResourceModel\Cform');
    }

}