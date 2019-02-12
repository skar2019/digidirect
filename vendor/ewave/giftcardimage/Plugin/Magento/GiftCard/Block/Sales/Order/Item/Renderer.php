<?php
namespace Ewave\GiftCardImage\Plugin\Magento\GiftCard\Block\Sales\Order\Item;

use Ewave\GiftCardImage\Model\QuoteItem as GiftcardQuoteItem;

class Renderer
{
    /**
     * @var GiftcardQuoteItem
     */
    protected $giftcardQuoteItem;

    /**
     * @param GiftcardQuoteItem $giftcardQuoteItem
     */
    public function __construct(
        GiftcardQuoteItem $giftcardQuoteItem
    ) {
        $this->giftcardQuoteItem = $giftcardQuoteItem;
    }

    /**
     * @param \Magento\GiftCard\Block\Sales\Order\Item\Renderer $subject
     * @param array $result
     * @return mixed
     */
    public function afterGetItemOptions(\Magento\GiftCard\Block\Sales\Order\Item\Renderer $subject, array $result)
    {
        $item = $subject->getOrderItem();
        if ($giftcardImage = $this->giftcardQuoteItem->getGiftcardImageByQuoteItemId($item->getQuoteItemId())) {
            $result[] = ['label' => __('Card Design'), 'value' => $giftcardImage->getTitle()];
        }
        return $result;
    }
}
