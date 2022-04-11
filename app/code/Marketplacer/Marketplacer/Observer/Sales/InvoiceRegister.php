<?php

namespace Marketplacer\Marketplacer\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Marketplacer\Marketplacer\Helper\Data as MarketplacerDataHelper;
use Marketplacer\SellerApi\Api\Data\OrderInterface;
use Marketplacer\SellerApi\Model\Order\SellerDataPreparer;

/**
 * Class InvoiceRegister
 * @package Marketplacer\Marketplacer\Observer\Sales
 */
class InvoiceRegister implements ObserverInterface
{
    /**
     * @var SellerDataPreparer
     */
    protected $sellerDataPreparer;

    /**
     * @var MarketplacerDataHelper
     */
    protected $marketplacerDataHelper;

    /**
     * InvoiceRegister constructor.
     * @param SellerDataPreparer $sellerDataPreparer
     * @param MarketplacerDataHelper $marketplacerDataHelper
     */
    public function __construct(
        SellerDataPreparer $sellerDataPreparer,
        MarketplacerDataHelper $marketplacerDataHelper
    ) {
        $this->sellerDataPreparer = $sellerDataPreparer;
        $this->marketplacerDataHelper = $marketplacerDataHelper;
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
        if ($this->marketplacerDataHelper->displaySellerBusinessNumber($invoice->getStoreId())) {
            $sellerBusinessNumbers = $this->sellerDataPreparer->getSellerBusinessNumbersBySalesItems($items);
            $invoice->setData(OrderInterface::SELLER_BUSINESS_NUMBERS, implode(', ', $sellerBusinessNumbers));
        }
    }
}
