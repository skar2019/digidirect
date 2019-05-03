<?php
namespace Ewave\AISales\Model\Import\Shipment\Model;

use Ewave\AISales\Model\Import\AbstractProcessor;

class Processor extends AbstractProcessor
{
    const COL_ORDER_INCREMENT_ID    = 'order_increment_id';
    const COL_ITEMS                 = 'items';
    const COL_COMMENTS              = 'comments';
    const COL_TRACKS                = 'tracks';

    /**
     * @var string
     */
    protected $entityTable = 'sales_shipment';

    /**
     * @var string
     */
    protected $entityTableGrid = 'sales_shipment_grid';
}
