<?php
namespace Ewave\AISales\Model\Import\Rma\Model\RelationPreparer;

use Ewave\AISales\Model\Import\AbstractRelationPreparer;
use Ewave\AISales\Model\Import\Rma\Model\Processor;
use Magento\Rma\Model\Rma\Status\History;

class Comments extends AbstractRelationPreparer
{
    const TABLE = 'magento_rma_status_history';

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
            History::RMA_ENTITY_ID,
            Processor::COL_COMMENTS
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
