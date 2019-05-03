<?php
namespace Ewave\AISales\Model\Import\Shipment\Model\RelationPreparer;

use Ewave\AISales\Model\Import\AbstractRelationPreparer;
use Ewave\AISales\Model\Import\Shipment\Model\Processor;
use Magento\Sales\Api\Data\ShipmentItemInterface;

class Items extends AbstractRelationPreparer
{
    const TABLE = 'sales_shipment_item';

    /**
     * @return string
     */
    public function getTable()
    {
        return self::TABLE;
    }

    /**
     * @param int $shipmentId
     * @param array $shipmentData
     * @return array
     */
    public function getRow($shipmentId, array $shipmentData)
    {
        return $this->getItemsData(
            $shipmentData,
            $shipmentId,
            ShipmentItemInterface::PARENT_ID,
            Processor::COL_ITEMS
        );
    }

    /**
     * @param int $shipmentId
     * @param array $shipmentData
     * @return array
     */
    public function getUpdatedRow($shipmentId, array $shipmentData)
    {
        return $this->getEntityItemsData(
            $shipmentData,
            $shipmentId,
            ShipmentItemInterface::PARENT_ID,
            ShipmentItemInterface::ENTITY_ID,
            Processor::COL_ITEMS,
            ShipmentItemInterface::SKU
        );
    }
}
