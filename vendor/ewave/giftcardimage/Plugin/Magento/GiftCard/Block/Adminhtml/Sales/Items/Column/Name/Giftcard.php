<?php
namespace Ewave\GiftCardImage\Plugin\Magento\GiftCard\Block\Adminhtml\Sales\Items\Column\Name;

use Ewave\GiftCardImage\Model\OrderItem as GiftcardOrderItem;

class Giftcard
{
    /**
     * @var GiftcardOrderItem
     */
    protected $giftcardOrderItem;

    /**
     * @param GiftcardOrderItem $giftcardOrderItem
     */
    public function __construct(
        GiftcardOrderItem $giftcardOrderItem
    ) {
        $this->giftcardOrderItem = $giftcardOrderItem;
    }

    /**
     * @param \Magento\GiftCard\Block\Adminhtml\Sales\Items\Column\Name\Giftcard $subject
     * @param array $result
     * @return mixed
     */
    public function afterGetOrderOptions(
        \Magento\GiftCard\Block\Adminhtml\Sales\Items\Column\Name\Giftcard $subject,
        array $result
    ) {
        $item = $subject->getItem();
        if ($giftcardImage = $this->giftcardOrderItem->getGiftcardImageByOrderItemId($item->getItemId())) {
            $result[] = ['label' => __('Card Design'), 'value' => $giftcardImage->getTitle()];
        }
        return $result;
    }
}
