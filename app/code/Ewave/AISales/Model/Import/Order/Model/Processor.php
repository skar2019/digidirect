<?php
namespace Ewave\AISales\Model\Import\Order\Model;

use Ewave\AISales\Model\Import\AbstractProcessor;

class Processor extends AbstractProcessor
{
    const COL_ITEMS                 = 'items';
    const COL_PAYMENTS              = 'payments';
    const COL_BILLING_ADDRESS       = 'billing_address';
    const COL_SHIPPING_ADDRESS      = 'shipping_address';
    const COL_STATUS_HISTORIES      = 'status_histories';

    /**
     * @var string
     */
    protected $entityTable = 'sales_order';

    /**
     * @var string
     */
    protected $entityTableGrid = 'sales_order_grid';
}
