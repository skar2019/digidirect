<?php
namespace Ewave\AISales\Model\Import\Rma\Model;

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
    protected $entityTable = 'magento_rma';

    /**
     * @var string
     */
    protected $entityTableGrid = 'magento_rma_grid';
}
