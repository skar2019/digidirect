<?php
namespace Ewave\AISales\Model\Import\Shipment\TypePreparer;

use Ewave\AISales\Model\Import\AbstractPreparer;
use Ewave\AISales\Model\Import\Shipment\Model\Processor;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentItemInterface;

class Items extends AbstractPreparer
{
    /**
     * @param array $shipment
     * @return array
     */
    public function prepareEntity(array &$shipment)
    {
        $shipment = $this->setItemsProductData(
            $shipment,
            ShipmentItemInterface::ENTITY_ID,
            Processor::COL_ITEMS
        );

        $shipment = $this->setItemsIds(
            $shipment,
            ShipmentItemInterface::ENTITY_ID,
            ShipmentItemInterface::ORDER_ITEM_ID,
            ShipmentInterface::ORDER_ID,
            Processor::COL_ITEMS
        );

        return $shipment;
    }
}
