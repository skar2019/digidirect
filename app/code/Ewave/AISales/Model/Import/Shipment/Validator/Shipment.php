<?php
namespace Ewave\AISales\Model\Import\Shipment\Validator;

use Ewave\AISales\Model\Import\Sales\Validator;
use Magento\Sales\Model\Order;

class Shipment extends Validator
{
    const TRUE = 'true';
    const SALES_ORDER = 'sales_order';
    const ORDER_INCREMENT_ID = 'order_increment_id';

    /**
     * @inheritdoc
     */
    public function isValid($value)
    {
        $isOrderVirtual = $this->dbHelper->isEntityExist(
            self::SALES_ORDER,
            [Order::INCREMENT_ID => $value[self::ORDER_INCREMENT_ID], Order::IS_VIRTUAL => 1]
        );
        if ($isOrderVirtual) {
            $this->messages[] = __('Virtual order cannot have any shipments');

            return false;
        }

        return $this->checkOrderRelationExist($value);
    }
}
