<?php

namespace Ewave\Collect\Model;

use Ewave\Collect\Helper\Data as CollectHelper;

class QuoteItemHandler
{
    /**
     * Collect Helper
     *
     * @var \Ewave\Collect\Helper\Data
     */
    protected $_collectHelper;

    /**
     * CollectQuantityValidator
     *
     * @var \Ewave\Collect\Model\CollectQuantityValidator
     */
    protected $_collectQuantityValidator;

    /**
     * QuoteItemHandler constructor.
     * 
     * @param CollectQuantityValidator $collectQuantityValidator
     * @param CollectHelper $collectHelper
     */
    public function __construct(
        \Ewave\Collect\Model\CollectQuantityValidator $collectQuantityValidator,
        \Ewave\Collect\Helper\Data $collectHelper
    ) {
        $this->_collectHelper = $collectHelper;
        $this->_collectQuantityValidator = $collectQuantityValidator;
    }

    /**
     * ChangeQuoteItemType
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param string $deliveryType
     * @param string $collectPlaceId
     * @param string $collectPlaceStorageName
     * @return void
     */
    public function changeQuoteItemCollectType($quoteItem, $deliveryType, $collectPlaceId, $collectPlaceStorageName)
    {
        if (
            $deliveryType == CollectHelper::DELIVERY_TYPE_COLLECT &&
            $collectPlaceId &&
            $collectPlaceStorageName
        ) {
            $quoteItem->setCollectPlaceId($collectPlaceId);
            $quoteItem->setCollectPlaceStorageName($collectPlaceStorageName);
        } elseif ($deliveryType == CollectHelper::DELIVERY_TYPE_DELIVER) {
            $quoteItem->setCollectPlaceId(null);
            $quoteItem->setCollectPlaceStorageName(null);
        }
    }

    /**
     * CheckQty
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param string $collectPlaceId
     * @param string $collectPlaceStorageName
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function checkQty($quoteItem, $collectPlaceId, $collectPlaceStorageName)
    {
        $result = true;
        $oldCollectPlaceId = $quoteItem->getCollectPlaceId();
        $oldCollectPlaceStorageName = $quoteItem->getCollectPlaceStorageName();
        if (!$quoteItem->getCollectPlaceId() && !$quoteItem->getCollectPlaceStorageName()) {
            $quoteItem->setCollectPlaceId($collectPlaceId);
            $quoteItem->setCollectPlaceStorageName($collectPlaceStorageName);
        }

        if ($this->_collectHelper->hasStockUpdateInterface() &&
            (($quoteItem->getCollectPlaceId() && $quoteItem->getCollectPlaceStorageName()))
        ) {
            try {
                if ($collectPlaceId === null) {
                    $collectPlaceId = $quoteItem->getCollectPlaceId();
                }
                $checkQty = $this->_collectQuantityValidator->checkProductQtyInSource($quoteItem, $collectPlaceId);
            } catch (\Exception $e) {
                $this->_collectHelper->logError($e->getMessage());
                $quoteItem->setUseOldQty(true);
                $result = false;
            }

            if (!$checkQty) {
                $quoteItem->setUseOldQty(true);
                $result = false;
            }
        }

        $quoteItem->setCollectPlaceId($oldCollectPlaceId);
        $quoteItem->setCollectPlaceStorageName($oldCollectPlaceStorageName);
        
        return $result;
    }
}
