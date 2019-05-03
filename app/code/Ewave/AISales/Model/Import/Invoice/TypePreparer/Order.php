<?php
namespace Ewave\AISales\Model\Import\Invoice\TypePreparer;

use Ewave\AISales\Model\Import\AbstractPreparer;
use Ewave\AISales\Model\Import\Invoice\Model\Processor;
use Magento\Sales\Api\Data\InvoiceInterface;

class Order extends AbstractPreparer
{
    /**
     * @param array $invoice
     * @return array
     */
    public function prepareEntity(array &$invoice)
    {
        $invoice = $this->setEntityOrderId(
            $invoice,
            InvoiceInterface::ORDER_ID,
            Processor::COL_ORDER_INCREMENT_ID
        );

        return $invoice;
    }
}
