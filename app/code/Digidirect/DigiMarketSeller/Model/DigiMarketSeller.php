<?php

namespace Digidirect\DigiMarketSeller\Model;

class DigiMarketSeller extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    
    protected function _construct()
    {
        $this->_init('Digidirect\DigiMarketSeller\Model\ResourceModel\DigiMarketSeller');
    }

}