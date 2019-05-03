<?php
namespace Ewave\AISales\Model\Import\Shipment\Model\RelationPreparer;

use Ewave\AISales\Model\Import\AbstractRelationPreparer;
use Ewave\AISales\Model\Import\Shipment\Model\Processor;
use Magento\Sales\Api\Data\ShipmentCommentInterface;

class Comments extends AbstractRelationPreparer
{
    const TABLE = 'sales_shipment_comment';

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
            ShipmentCommentInterface::PARENT_ID,
            Processor::COL_COMMENTS
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
