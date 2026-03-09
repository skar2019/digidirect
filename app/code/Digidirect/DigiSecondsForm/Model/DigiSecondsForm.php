<?php

namespace Digidirect\DigiSecondsForm\Model;

class DigiSecondsForm extends \Magento\Framework\Model\AbstractModel
{
    public const CONTECT_ID = 'contect_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    
    protected function _construct()
    {
        $this->_init('Digidirect\DigiSecondsForm\Model\ResourceModel\DigiSecondsForm');
    }

}