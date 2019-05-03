<?php
namespace Ewave\AISales\Model\Import\Invoice\TypePreparer;

use Ewave\AISales\Model\Import\AbstractPreparer;
use Ewave\AISales\Model\Import\Invoice\Model\Processor;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\InvoiceItemInterface;

class Items extends AbstractPreparer
{
    /**
     * @param array $invoice
     * @return array
     */
    public function prepareEntity(array &$invoice)
    {
        $invoice = $this->setItemsProductData(
            $invoice,
            InvoiceItemInterface::ENTITY_ID,
            Processor::COL_ITEMS
        );

        $invoice = $this->setItemsIds(
            $invoice,
            InvoiceItemInterface::ENTITY_ID,
            InvoiceItemInterface::ORDER_ITEM_ID,
            InvoiceInterface::ORDER_ID,
            Processor::COL_ITEMS
        );
        return $invoice;
    }
}
