<?php
namespace Ewave\AISales\Model\Import\CreditMemo\Model;

use Ewave\AISales\Model\Import\AbstractProcessor;

class Processor extends AbstractProcessor
{
    const COL_ORDER_INCREMENT_ID    = 'order_increment_id';
    const COL_ITEMS                 = 'items';
    const COL_COMMENTS              = 'comments';

    /**
     * @var string
     */
    protected $entityTable = 'sales_creditmemo';

    /**
     * @var string
     */
    protected $entityTableGrid = 'sales_creditmemo_grid';
}
