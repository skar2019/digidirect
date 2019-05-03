<?php
namespace Ewave\AISales\Model\Import\Invoice\Model;

use Ewave\AISales\Model\Import\AbstractProcessor;

class Processor extends AbstractProcessor
{
    const COL_ORDER_INCREMENT_ID    = 'order_increment_id';
    const COL_ITEMS                 = 'items';
    const COL_COMMENTS              = 'comments';

    /**
     * @var string
     */
    protected $entityTable = 'sales_invoice';

    /**
     * @var string
     */
    protected $entityTableGrid = 'sales_invoice_grid';
}
