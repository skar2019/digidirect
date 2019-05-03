<?php
namespace Ewave\AISales\Model\Import\Shipment\TypePreparer;

use Ewave\AISales\Model\Import\AbstractPreparer;
use Ewave\AISales\Model\Import\Shipment\Model\Processor;
use Magento\Sales\Api\Data\ShipmentInterface;

class Order extends AbstractPreparer
{
    /**
     * @param array $shipment
     * @return array
     */
    public function prepareEntity(array &$shipment)
    {
        $shipment = $this->setEntityOrderId(
            $shipment,
            ShipmentInterface::ORDER_ID,
            Processor::COL_ORDER_INCREMENT_ID
        );

        return $shipment;
    }
}
