<?php

namespace Marketplacer\SellerApi\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Marketplacer\SellerApi\Model\Order\SellerDataPreparer;
use Marketplacer\SellerApi\Api\Data\OrderInterface;

class InvoiceRegister implements ObserverInterface
{
    /**
     * @var SellerDataPreparer
     */
    protected $sellerDataPreparer;

    /**
     * InvoiceRegister constructor.
     * @param SellerDataPreparer $sellerDataPreparer
     */
    public function __construct(
        SellerDataPreparer $sellerDataPreparer
    ) {
        $this->sellerDataPreparer = $sellerDataPreparer;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /**
         * @var \Magento\Sales\Model\Order\Invoice $invoice
         */

        $invoice = $observer->getEvent()->getData('invoice');

        $items = $invoice->getAllItems();

        $sellerNames = $this->sellerDataPreparer->getSellerNamesBySalesItems($items);
        $invoice->setData(OrderInterface::SELLER_NAMES, implode(', ', $sellerNames));
    }
}
