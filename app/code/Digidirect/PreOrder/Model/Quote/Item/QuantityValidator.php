<?php
namespace Digidirect\PreOrder\Model\Quote\Item;

class QuantityValidator extends \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator
{
    /**
     * @var \Digidirect\PreOrder\Helper\Data
     */
    protected $preOrderHelper;

    /**
     * QuantityValidator constructor.
     * @param \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option $optionInitializer
     * @param \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem $stockItemInitializer
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockState
     * @param \Digidirect\PreOrder\Helper\Data $preOrderHelper
     */
    public function __construct(
        \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\Option $optionInitializer,
        \Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem $stockItemInitializer,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        \Digidirect\PreOrder\Helper\Data $preOrderHelper
    ) {
        parent::__construct(
            $optionInitializer,
            $stockItemInitializer,
            $stockRegistry,
            $stockState
        );
        $this->preOrderHelper = $preOrderHelper;
    }

    /**
     * Check product inventory data when quote item quantity declaring
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function validate(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $quoteItem \Magento\Quote\Model\Quote\Item */
        $quoteItem = $observer->getEvent()->getItem();

        // Check for rest api calls (situation when "product" key is not exist yet)
        if (!$quoteItem->getData('product')) {
            return;
        }

        $qty = $quoteItem->getQty();

        /** @var \Magento\CatalogInventory\Model\Stock\Item $stockItem */
        $stockItem = $this->stockRegistry->getStockItem(
            $quoteItem->getProduct()->getId(),
            $quoteItem->getProduct()->getStore()->getWebsiteId()
        );

        $parentStockItem = false;

        /**
         * Check if product in stock. For composite products check base (parent) item stock status
         */
        if ($quoteItem->getParentItem()) {
            $product = $quoteItem->getParentItem()->getProduct();
            $parentStockItem = $this->stockRegistry->getStockItem(
                $product->getId(),
                $product->getStore()->getWebsiteId()
            );
        }

        $isPreOrderEnabled = $this->preOrderHelper->checkStockItemQty($stockItem)
            && $this->preOrderHelper->checkQtyAvailability($stockItem, $qty);

        if ($stockItem) {
            if (!$stockItem->getIsInStock() || $parentStockItem && !$parentStockItem->getIsInStock()) {
                if ($isPreOrderEnabled) {
                    $this->_removeErrorsFromQuoteAndItem($quoteItem, \Magento\CatalogInventory\Helper\Data::ERROR_QTY);
                }
            }
        }

        /**
         * Check item for options
         */
        if (($options = $quoteItem->getQtyOptions()) && $qty > 0) {
            $qty = $quoteItem->getProduct()->getTypeInstance()->prepareQuoteItemQty($qty, $quoteItem->getProduct());
            $quoteItem->setData('qty', $qty);
            if ($stockItem) {
                $result = $this->stockState->checkQtyIncrements(
                    $quoteItem->getProduct()->getId(),
                    $qty,
                    $quoteItem->getProduct()->getStore()->getWebsiteId()
                );
                if ($result->getHasError() && $isPreOrderEnabled) {
                    $this->_removeErrorsFromQuoteAndItem($quoteItem, \Magento\CatalogInventory\Helper\Data::ERROR_QTY);
                }
            }

            foreach ($options as $option) {
                $result = $this->optionInitializer->initialize($option, $quoteItem, $qty);
                if ($result->getHasError() && $isPreOrderEnabled) {
                    $this->_removeErrorsFromQuoteAndItem($quoteItem, \Magento\CatalogInventory\Helper\Data::ERROR_QTY);
                }
            }
        } else {
            $result = $this->stockItemInitializer->initialize($stockItem, $quoteItem, $qty);
            if ($result->getHasError() && $isPreOrderEnabled) {
                $this->_removeErrorsFromQuoteAndItem($quoteItem, \Magento\CatalogInventory\Helper\Data::ERROR_QTY);
            }
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param int $code
     * @return void
     */
    protected function _removeErrorsFromQuoteAndItem($item, $code)
    {
        parent::_removeErrorsFromQuoteAndItem($item, $code);
        $item->setUseOldQty(false);
    }
}
