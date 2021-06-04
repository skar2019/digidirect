<?php

namespace Ewave\FreeGift\Plugin\Magento\Tax\Block\Item\Price;

/**
 * Class Renderer
 * @package Ewave\FreeGift\Plugin\Magento\Tax\Block\Item\Price
 */
class Renderer
{
    /**
     * @var \Ewave\FreeGift\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Ewave\FreeGift\Helper\Config
     */
    protected $_helperConfig;

    /**
     * Renderer constructor.
     * @param \Ewave\FreeGift\Helper\Data $helper
     * @param \Ewave\FreeGift\Helper\Config $helperConfig
     */
    public function __construct(
        \Ewave\FreeGift\Helper\Data $helper,
        \Ewave\FreeGift\Helper\Config $helperConfig
    ) {
        $this->_helper = $helper;
        $this->_helperConfig = $helperConfig;
    }

    /**
     * @param \Magento\Tax\Block\Item\Price\Renderer $subject
     * @param \Closure $procede
     * @param float $price
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundFormatPrice(
        \Magento\Tax\Block\Item\Price\Renderer $subject,
        \Closure $procede,
        $price
    ) {
        $product = $subject->getItem();

        if (($product instanceof \Magento\Quote\Model\Quote\Item ||
            $product instanceof \Magento\Sales\Model\Order\Item) &&
            $this->_helper->isFreeGiftItem($product) &&
            $this->_helperConfig->isHideFreeItemPrice()
        ) {
            $priceMessage = $this->_helperConfig->getMessageForHiddenFreeItemPrice();
            return '<span class="price">' . $priceMessage . '</span>';
        } else {
            return $procede($price);
        }
    }
}
