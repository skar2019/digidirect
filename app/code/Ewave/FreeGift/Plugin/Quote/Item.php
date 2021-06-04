<?php

namespace Ewave\FreeGift\Plugin\Quote;

use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Catalog\Model\Product;
use Ewave\FreeGift\Model\Cart\Item as CartItem;

class Item
{
    /**
     * @var CartItem
     */
    protected $_giftItem;

    /**
     * Item constructor.
     *
     * @param CartItem $giftItem
     */
    public function __construct(CartItem $giftItem)
    {
        $this->_giftItem = $giftItem;
    }

    /**
     * @param QuoteItem $subject
     * @param mixed $value
     * @return array
     */
    public function beforeSetPrice(QuoteItem $subject, $value)
    {
        if ($this->_giftItem->isFreeGiftItem($subject)) {
            return [0];
        }
        return [$value];
    }

    /**
     * @param QuoteItem $subject
     * @param \Closure $proceed
     * @param Product $product
     * @return bool
     */
    public function aroundRepresentProduct(
        QuoteItem $subject,
        \Closure $proceed,
        Product $product
    ) {
        $result = $proceed($product);
        if ($result) {
            $productRuleId = $product->getData(CartItem::FREE_GIFT_KEY);
            $productIsHiddenForCustomer = (bool)$product->getData(CartItem::FREE_GIFT_IS_HIDDEN_FOR_CUSTOMER);
            $itemRuleId = $this->_giftItem->getRuleId($subject);
            $itemIsHiddenForCustomer = $this->_giftItem->isHiddenForCustomerGiftItem($subject);
            $result = $productRuleId === $itemRuleId && $productIsHiddenForCustomer === $itemIsHiddenForCustomer;
        }
        return $result;
    }
}
