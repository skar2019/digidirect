<?php
namespace Ewave\AISales\Model\Import\CreditMemo\Model\RelationPreparer;

use Ewave\AISales\Model\Import\AbstractRelationPreparer;
use Ewave\AISales\Model\Import\CreditMemo\Model\Processor;
use Magento\Sales\Api\Data\CreditmemoItemInterface;

class Items extends AbstractRelationPreparer
{
    const TABLE = 'sales_creditmemo_item';

    /**
     * @return string
     */
    public function getTable()
    {
        return self::TABLE;
    }

    /**
     * @param int $creditmemoId
     * @param array $creditmemoData
     * @return array
     */
    public function getRow($creditmemoId, array $creditmemoData)
    {
        return $this->getItemsData(
            $creditmemoData,
            $creditmemoId,
            CreditmemoItemInterface::PARENT_ID,
            Processor::COL_ITEMS
        );
    }

    /**
     * @param int $creditmemoId
     * @param array $creditmemoData
     * @return array
     */
    public function getUpdatedRow($creditmemoId, array $creditmemoData)
    {
        return $this->getEntityItemsData(
            $creditmemoData,
            $creditmemoId,
            CreditmemoItemInterface::PARENT_ID,
            CreditmemoItemInterface::ENTITY_ID,
            Processor::COL_ITEMS,
            CreditmemoItemInterface::SKU
        );
    }
}
