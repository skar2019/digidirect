<?php

namespace Marketplacer\BrandApi\Plugin\Quote\Model\Item;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Sales\Api\Data\OrderItemInterface;
use Marketplacer\BrandApi\Model\Order\BrandDataPreparer;
use Marketplacer\BrandApi\Api\BrandAttributeRetrieverInterface;
use Marketplacer\BrandApi\Api\Data\OrderItemInterface as BrandOrderItemInterface;

class ToOrderItemPlugin
{
    /**
     * @var BrandAttributeRetrieverInterface
     */
    protected $brandAttributeRetriever;

    /**
     * @var BrandDataPreparer
     */
    protected $brandDataPreparer;

    /**
     * @param BrandAttributeRetrieverInterface $brandAttributeRetriever
     * @param BrandDataPreparer $brandDataPreparer
     */
    public function __construct(
        BrandAttributeRetrieverInterface $brandAttributeRetriever,
        BrandDataPreparer $brandDataPreparer
    ) {
        $this->brandAttributeRetriever = $brandAttributeRetriever;
        $this->brandDataPreparer = $brandDataPreparer;
    }

    /**
     * @param ToOrderItem $subject
     * @param OrderItemInterface $orderItem
     * @param AbstractItem $item
     * @param array $additional
     * @return OrderItemInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterConvert(
        ToOrderItem $subject,
        OrderItemInterface $orderItem,
        AbstractItem $item,
        $additional = []
    ) {
        $brandId = $this->brandDataPreparer->getBrandIdByQuoteItem($item);
        if (!$brandId) {
            return $orderItem;
        }
        $brandName = $this->brandDataPreparer->getBrandNameById($brandId, $orderItem->getStoreId());
        $orderItem->setData(BrandOrderItemInterface::BRAND_ID, $brandId);
        $orderItem->setData(BrandOrderItemInterface::BRAND_NAME, $brandName);
        return $orderItem;
    }
}
