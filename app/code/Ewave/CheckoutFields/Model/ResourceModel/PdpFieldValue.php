<?php

namespace Ewave\CheckoutFields\Model\ResourceModel;

use Ewave\CheckoutFields\Api\Data\PdpFieldValueInterface;
use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class PdpFieldValue
 * @package Ewave\CheckoutFields\Model\ResourceModel
 */
class PdpFieldValue extends AbstractDb
{
    const PDP_VALUES_TABLE = 'ewave_checkout_fields_pdp_field_value';

    /**
     * Initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init(static::PDP_VALUES_TABLE, PdpFieldValueInterface::ENTITY_ID);
    }
}
