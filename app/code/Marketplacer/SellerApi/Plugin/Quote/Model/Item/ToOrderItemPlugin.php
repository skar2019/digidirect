<?php

namespace Marketplacer\SellerApi\Plugin\Quote\Model\Item;

use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Sales\Api\Data\OrderItemInterface;
use Marketplacer\SellerApi\Model\Order\SellerDataPreparer;
use Marketplacer\SellerApi\Api\Data\OrderItemInterface as SellerOrderItemInterface;
use Marketplacer\SellerApi\Api\SellerAttributeRetrieverInterface;

class ToOrderItemPlugin
{
    /**
     * @var SellerAttributeRetrieverInterface
     */
    protected $sellerAttributeRetriever;

    /**
     * @var SellerDataPreparer
     */
    protected $sellerDataPreparer;

    /**
     * @param SellerAttributeRetrieverInterface $sellerAttributeRetriever
     * @param SellerDataPreparer $sellerDataPreparer
     */
    public function __construct(
        SellerAttributeRetrieverInterface $sellerAttributeRetriever,
        SellerDataPreparer $sellerDataPreparer
    ) {
        $this->sellerAttributeRetriever = $sellerAttributeRetriever;
        $this->sellerDataPreparer = $sellerDataPreparer;
    }

    /**
     * @param ToOrderItem $subject
     * @param OrderItemInterface $orderItem
     * @param AbstractItem $item
     * @param array $additional
     * @return OrderItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterConvert(
        ToOrderItem $subject,
        OrderItemInterface $orderItem,
        AbstractItem $item,
        $additional = []
    ) {
        $sellerId = $this->sellerDataPreparer->getSellerIdByQuoteItem($item);
        $sellerName = $this->sellerDataPreparer->getSellerNameById($sellerId, $orderItem->getStoreId());
        $sellerBusinessNumber = $this->sellerDataPreparer
            ->getSellerBusinessNumberById($sellerId, $orderItem->getStoreId());

        $orderItem->setData(SellerOrderItemInterface::SELLER_ID, $sellerId);
        $orderItem->setData(SellerOrderItemInterface::SELLER_NAME, $sellerName);
        $orderItem->setData(SellerOrderItemInterface::SELLER_BUSINESS_NUMBER, $sellerBusinessNumber);

        return $orderItem;
    }
}
