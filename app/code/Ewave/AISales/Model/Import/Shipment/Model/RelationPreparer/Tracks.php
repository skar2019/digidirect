<?php
namespace Ewave\AISales\Model\Import\Shipment\Model\RelationPreparer;

use Ewave\AISales\Model\Import\AbstractRelationPreparer;
use Ewave\AISales\Model\Import\Shipment\Model\Processor;
use Magento\Sales\Api\Data\ShipmentTrackInterface;

class Tracks extends AbstractRelationPreparer
{
    const TABLE = 'sales_shipment_track';

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
     * @return mixed
     */
    public function getRow($shipmentId, array $shipmentData)
    {
        return $this->getItemsData(
            $shipmentData,
            $shipmentId,
            ShipmentTrackInterface::PARENT_ID,
            Processor::COL_TRACKS
        );
    }

    /**
     * @param int $shipmentId
     * @param array $shipmentData
     * @return array
     */
    public function getUpdatedRow($shipmentId, array $shipmentData)
    {
        return $this->getRow($shipmentId, $shipmentData);
    }
}
