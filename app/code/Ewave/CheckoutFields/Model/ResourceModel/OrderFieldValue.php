<?php

namespace Ewave\CheckoutFields\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class OrderFieldValue
 * @package Ewave\CheckoutFields\Model\ResourceModel
 */
class OrderFieldValue extends AbstractDb
{
    /**
     * Initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ewave_checkout_fields_order_field_value', 'entity_id');
    }

    /**
     * Insert fields values into table
     *
     * @param array $dataToSave
     * @return int
     */
    public function saveCustomCheckoutValuesToOrder(array $dataToSave)
    {
        $adapter = $this->getConnection();
        return $adapter->insertOnDuplicate($this->getMainTable(), $dataToSave, ['value']);
    }

    /**
     * @param int $orderId
     * @param string $fieldId
     * @return string
     */
    public function getCustomCheckoutOrderFieldValue($orderId, $fieldId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select();
        $select->from($this->getMainTable(), ['value'])
            ->where('order_id = ?', $orderId)
            ->where('field_id = ?', $fieldId)
            ->limit(1);
        $result = $adapter->fetchOne($select);
        if (empty($result)) {
            return null;
        }
        return unserialize($result);
    }
}
