<?php
namespace Ewave\AISales\Model\Import\Rma\Model\RelationPreparer;

use Ewave\AISales\Model\Import\AbstractRelationPreparer;
use Ewave\AISales\Model\Import\Rma\Model\Processor;
use Magento\Rma\Model\Shipping;

class Tracks extends AbstractRelationPreparer
{
    const TABLE = 'magento_rma_shipping_label';

    /**
     * @return string
     */
    public function getTable()
    {
        return self::TABLE;
    }

    /**
     * @param int $rmaId
     * @param array $rmaData
     * @return mixed
     */
    public function getRow($rmaId, array $rmaData)
    {
        return $this->getItemsData(
            $rmaData,
            $rmaId,
            Shipping::RMA_ENTITY_ID,
            Processor::COL_TRACKS
        );
    }

    /**
     * @param int $rmaId
     * @param array $rmaData
     * @return array
     */
    public function getUpdatedRow($rmaId, array $rmaData)
    {
        return $this->getRow($rmaId, $rmaData);
    }
}
