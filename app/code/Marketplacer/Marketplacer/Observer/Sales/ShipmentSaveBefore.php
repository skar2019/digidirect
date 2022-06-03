<?php

namespace Marketplacer\Marketplacer\Observer\Sales;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Marketplacer\Marketplacer\Helper\Data as MarketplacerDataHelper;
use Marketplacer\SellerApi\Api\Data\OrderInterface;
use Marketplacer\SellerApi\Model\Order\SellerDataPreparer;

/**
 * Class ShipmentSaveBefore
 * @package Marketplacer\Marketplacer\Observer\Sales
 */
class ShipmentSaveBefore implements ObserverInterface
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
     * ShipmentSaveBefore constructor.
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
     * @returns void
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
        if ($this->marketplacerDataHelper->displaySellerBusinessNumber($shipment->getStoreId())) {
            $sellerBusinessNumbers = $this->sellerDataPreparer->getSellerBusinessNumbersBySalesItems($items);
            $shipment->setData(OrderInterface::SELLER_BUSINESS_NUMBERS, implode(', ', $sellerBusinessNumbers));
        }
    }
}
