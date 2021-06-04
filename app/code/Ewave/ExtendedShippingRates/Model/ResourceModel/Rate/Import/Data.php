<?php
namespace Ewave\ExtendedShippingRates\Model\ResourceModel\Rate\Import;

use Ewave\ExtendedShippingRates\Model\Carrier;
use Ewave\ExtendedShippingRates\Model\Carrier\Method\Rate;

/**
 * Class Data
 * @package Ewave\ExtendedShippingRates\Model\ResourceModel\Rate\Import
 */
class Data extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init($this->getTable(Carrier::RATE_TABLE_NAME), Rate::RATE_ID);
    }

    /**
     * Save import rows bunch.
     *
     * @param array $data
     * @return int
     */
    public function saveBunch(array $data)
    {
        return $this->getConnection()->insertMultiple(
            $this->getMainTable(),
            $data
        );
    }
}
