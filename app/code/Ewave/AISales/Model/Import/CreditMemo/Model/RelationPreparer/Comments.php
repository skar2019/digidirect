<?php
namespace Ewave\AISales\Model\Import\CreditMemo\Model\RelationPreparer;

use Ewave\AISales\Model\Import\AbstractRelationPreparer;
use Ewave\AISales\Model\Import\CreditMemo\Model\Processor;
use Magento\Sales\Api\Data\CreditmemoCommentInterface;

class Comments extends AbstractRelationPreparer
{
    const TABLE = 'sales_creditmemo_comment';

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
     * @return mixed
     */
    public function getRow($creditmemoId, array $creditmemoData)
    {
        return $this->getItemsData(
            $creditmemoData,
            $creditmemoId,
            CreditmemoCommentInterface::PARENT_ID,
            Processor::COL_COMMENTS
        );
    }

    /**
     * @param int $creditmemoId
     * @param array $creditmemoData
     * @return array
     */
    public function getUpdatedRow($creditmemoId, array $creditmemoData)
    {
        return $this->getRow($creditmemoId, $creditmemoData);
    }
}
