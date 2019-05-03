<?php
namespace Ewave\AISales\Model\Import\Invoice\Model\RelationPreparer;

use Ewave\AISales\Model\Import\AbstractRelationPreparer;
use Ewave\AISales\Model\Import\Invoice\Model\Processor;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;

class Comments extends AbstractRelationPreparer
{
    const TABLE = 'sales_invoice_comment';

    /**
     * @return string
     */
    public function getTable()
    {
        return self::TABLE;
    }

    /**
     * @param int $invoiceId
     * @param array $invoiceData
     * @return mixed
     */
    public function getRow($invoiceId, array $invoiceData)
    {
        return $this->getItemsData(
            $invoiceData,
            $invoiceId,
            OrderStatusHistoryInterface::PARENT_ID,
            Processor::COL_COMMENTS
        );
    }

    /**
     * @param int $invoiceId
     * @param array $invoiceData
     * @return array
     */
    public function getUpdatedRow($invoiceId, array $invoiceData)
    {
        return $this->getRow($invoiceId, $invoiceData);
    }
}
