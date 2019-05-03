<?php
namespace Ewave\AISales\Model\Import\Invoice\Model\RelationPreparer;

use Ewave\AISales\Model\Import\AbstractRelationPreparer;
use Ewave\AISales\Model\Import\Invoice\Model\Processor;
use Magento\Sales\Api\Data\InvoiceItemInterface;

class Items extends AbstractRelationPreparer
{
    const TABLE = 'sales_invoice_item';

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
     * @return array
     */
    public function getRow($invoiceId, array $invoiceData)
    {
        return $this->getItemsData(
            $invoiceData,
            $invoiceId,
            InvoiceItemInterface::PARENT_ID,
            Processor::COL_ITEMS
        );
    }

    /**
     * @param int $invoiceId
     * @param array $invoiceData
     * @return array
     */
    public function getUpdatedRow($invoiceId, array $invoiceData)
    {
        return $this->getEntityItemsData(
            $invoiceData,
            $invoiceId,
            InvoiceItemInterface::PARENT_ID,
            InvoiceItemInterface::ENTITY_ID,
            Processor::COL_ITEMS,
            InvoiceItemInterface::SKU
        );
    }
}
