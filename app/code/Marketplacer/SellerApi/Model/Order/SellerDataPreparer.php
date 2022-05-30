<?php

namespace Marketplacer\SellerApi\Model\Order;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Sales\Model\AbstractModel as SalesAbstractModel;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Shipment\Item as ShipmentItem;
use Magento\Quote\Model\Quote\Address\Item as AddressItem;
use Marketplacer\SellerApi\Api\SellerRepositoryInterface;
use Marketplacer\SellerApi\Api\Data\OrderItemInterface;
use Marketplacer\SellerApi\Api\SellerAttributeRetrieverInterface;

class SellerDataPreparer
{
    /**
     * @var SellerRepositoryInterface
     */
    protected $sellerRepository;

    /**
     * @var SellerAttributeRetrieverInterface
     */
    protected $sellerAttributeRetriever;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param SellerRepositoryInterface $sellerRepository
     * @param SellerAttributeRetrieverInterface $sellerAttributeRetriever
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        SellerRepositoryInterface $sellerRepository,
        SellerAttributeRetrieverInterface $sellerAttributeRetriever,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
        $this->sellerRepository = $sellerRepository;
        $this->sellerAttributeRetriever = $sellerAttributeRetriever;
    }

    /**
     * @param array $sellerIds
     * @param int | string | null $storeId
     * @return array
     */
    public function getSellerNamesByIds(array $sellerIds, $storeId = null)
    {
        $result = [];
        foreach ($sellerIds as $sellerId) {
            $sellerName = $this->getSellerNameById($sellerId, $storeId);
            if (!empty($sellerName)) {
                $result[] = $sellerName;
            }
        }
        return $result;
    }

    /**
     * @param array $sellerIds
     * @param int | string | null $storeId
     * @return array
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getSellerBusinessNumbersByIds(array $sellerIds, $storeId = null)
    {
        $result = [];
        foreach ($sellerIds as $sellerId) {
            $sellerName = $this->getSellerBusinessNumberById($sellerId, $storeId);
            if (!empty($sellerName)) {
                $result[] = $sellerName;
            }
        }
        return $result;
    }

    /**
     * @param int|null $sellerId
     * @param int | string | null $storeId
     * @return string|null
     */
    public function getSellerNameById($sellerId, $storeId = null)
    {
        try {
            $seller = $this->sellerRepository->getById($sellerId, $storeId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
        return $seller->getName();
    }

    /**
     * @param int|null $sellerId
     * @param int | string | null $storeId
     * @return string|null
     * @throws LocalizedException
     */
    public function getSellerBusinessNumberById($sellerId, $storeId = null)
    {
        try {
            $seller = $this->sellerRepository->getById($sellerId, $storeId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
        return $seller->getBusinessNumber();
    }

    /**
     * @param AbstractItem[] $quoteItems
     * @return array
     * @throws LocalizedException
     */
    public function getSellerIdsByQuoteItems(array $quoteItems)
    {
        $result = [];
        foreach ($quoteItems as $quoteItem) {
            $sellerId = $this->getSellerIdByQuoteItem($quoteItem);
            if ($sellerId) {
                $result[$sellerId] = $sellerId;
            }
        }
        return $result;
    }

    /**
     * @param AbstractItem $quoteItem
     * @return mixed
     * @throws LocalizedException
     */
    public function getSellerIdByQuoteItem(AbstractItem $quoteItem)
    {
        $sellerAttributeCode = $this->sellerAttributeRetriever->getAttributeCode();
        return $quoteItem->getProduct()->getData($sellerAttributeCode);
    }

    /**
     * @param array $salesItems
     * @return array
     */
    public function getSellerNamesBySalesItems(array $salesItems)
    {
        $result = [];
        foreach ($salesItems as $salesItem) {
            $sellerId = $this->getSellerIdBySalesItem($salesItem);
            if ($sellerId) {
                $sellerName = $this->getSellerNameBySalesItem($salesItem);
                if (!empty($sellerName)) {
                    $result[$sellerId] = $sellerName;
                }
            }
        }
        return $result;
    }

    /**
     * @param array $salesItems
     * @return array
     */
    public function getSellerBusinessNumbersBySalesItems(array $salesItems)
    {
        $result = [];
        foreach ($salesItems as $salesItem) {
            $sellerId = $this->getSellerIdBySalesItem($salesItem);
            if ($sellerId) {
                $businessNumber = $this->getSellerBusinessNumberBySalesItem($salesItem);
                if (!empty($businessNumber)) {
                    $result[$sellerId] = $businessNumber;
                }
            }

        }
        return $result;
    }

    /**
     * @param SalesAbstractModel $salesItem
     * @return string
     */
    public function getSellerIdBySalesItem($salesItem)
    {
        $orderItem = $salesItem;
        if ($salesItem instanceof AddressItem) {
            return null;
        }
        if ($salesItem instanceof InvoiceItem || $salesItem instanceof ShipmentItem) {
            $orderItem = $salesItem->getOrderItem();
        }
        return $orderItem->getData(OrderItemInterface::SELLER_ID);
    }

    /**
     * @param SalesAbstractModel $salesItem
     * @return string
     */
    public function getSellerNameBySalesItem($salesItem)
    {
        $orderItem = $salesItem;
        if ($salesItem instanceof AddressItem) {
            return null;
        }
        if ($salesItem instanceof InvoiceItem || $salesItem instanceof ShipmentItem) {
            $orderItem = $salesItem->getOrderItem();
        }
        return $orderItem->getData(OrderItemInterface::SELLER_NAME);
    }

    /**
     * @param SalesAbstractModel $salesItem
     * @return string
     */
    public function getSellerBusinessNumberBySalesItem($salesItem)
    {
        $orderItem = $salesItem;
        if ($salesItem instanceof AddressItem) {
            return null;
        }
        if ($salesItem instanceof InvoiceItem || $salesItem instanceof ShipmentItem) {
            $orderItem = $salesItem->getOrderItem();
        }
        return $orderItem->getData(OrderItemInterface::SELLER_BUSINESS_NUMBER);
    }
}
