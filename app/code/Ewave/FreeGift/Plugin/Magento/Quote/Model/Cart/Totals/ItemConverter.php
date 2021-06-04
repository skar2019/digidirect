<?php

namespace Ewave\FreeGift\Plugin\Magento\Quote\Model\Cart\Totals;

/**
 * Class ItemConverter
 * @package Ewave\FreeGift\Plugin\Magento\Quote\Model\Cart\Totals
 */
class ItemConverter
{
    const ITEM_TOTALS_FREE_GIFT_ITEM_FLAG = 'ewave_is_free_gift_item';
    const ITEM_TOTALS_FREE_GIFT_ITEM_PRICE = 'ewave_free_gift_price';

    /**
     * @var \Ewave\FreeGift\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Ewave\FreeGift\Helper\Config
     */
    protected $_configHelper;

    /**
     * @var \Magento\Quote\Api\Data\TotalsItemExtensionFactory
     */
    protected $_totalsItemExtensionFactory;

    /**
     * ItemConverter constructor.
     * @param \Ewave\FreeGift\Helper\Data $dataHelper
     * @param \Ewave\FreeGift\Helper\Config $configHelper
     * @param \Magento\Quote\Api\Data\TotalsItemExtensionFactory $totalsItemExtensionFactory
     */
    public function __construct(
        \Ewave\FreeGift\Helper\Data $dataHelper,
        \Ewave\FreeGift\Helper\Config $configHelper,
        \Magento\Quote\Api\Data\TotalsItemExtensionFactory $totalsItemExtensionFactory
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_configHelper = $configHelper;
        $this->_totalsItemExtensionFactory = $totalsItemExtensionFactory;
    }

    /**
     * Insert an additional data to Totals item to mark a Free-gift product.
     *
     * @param \Magento\Quote\Model\Cart\Totals\ItemConverter $subject
     * @param \Closure $procede
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundModelToDataObject(
        \Magento\Quote\Model\Cart\Totals\ItemConverter $subject,
        \Closure $procede,
        \Magento\Quote\Model\Quote\Item $item
    ) {
        $result = $procede($item);

        if ($this->_dataHelper->isFreeGiftItem($item) && $this->_configHelper->isHideFreeItemPrice()) {
            $priceMessage = $this->_configHelper->getMessageForHiddenFreeItemPrice();

            $extensionAttributes = $item->getExtensionAttributes();
            if ($extensionAttributes === null) {
                $extensionAttributes = $this->_totalsItemExtensionFactory->create();
            }
            $extensionAttributes->setEwaveIsFreeGiftItem(true);
            $extensionAttributes->setEwaveFreeGiftPrice($priceMessage);
            $result->setExtensionAttributes($extensionAttributes);
        }

        return $result;
    }
}
