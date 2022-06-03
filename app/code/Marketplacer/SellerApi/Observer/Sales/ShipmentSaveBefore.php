<?php

namespace Marketplacer\SellerApi\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Marketplacer\SellerApi\Model\Order\SellerDataPreparer;
use Marketplacer\SellerApi\Api\Data\OrderInterface;

class ShipmentSaveBefore implements ObserverInterface
{
    /**
     * @var SellerDataPreparer
     */
    protected $sellerDataPreparer;

    /**
     * ShipmentSaveBefore constructor.
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
         * @var \Magento\Sales\Model\Order\Shipment $shipment
         */

        $shipment = $observer->getEvent()->getData('shipment');
        if ($shipment->getId()) {
            return;
        }

        $items = $shipment->getAllItems();

        $sellerNames = $this->sellerDataPreparer->getSellerNamesBySalesItems($items);
        $shipment->setData(OrderInterface::SELLER_NAMES, implode(', ', $sellerNames));
    }
}
