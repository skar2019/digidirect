<?php
namespace Ewave\GiftCardImage\Plugin\Magento\Checkout\CustomerData;

use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;
use Ewave\GiftCardImage\Model\QuoteItem as GiftcardQuoteItem;
use Ewave\GiftCardImage\Helper\ImageFactory as GiftcardHelperFactory;
use Magento\GiftCard\Model\Catalog\Product\Type\Giftcard;

class DefaultItem
{
    /**
     * @var GiftcardQuoteItem
     */
    protected $giftcardQuoteItem;

    /**
     * @var GiftcardHelperFactory
     */
    protected $giftcardHelperFactory;

    /**
     * @var string
     */
    protected $giftcardImageType;

    /**
     * @param GiftcardHelperFactory $giftcardHelperFactory
     * @param GiftcardQuoteItem $giftcardQuoteItem
     * @param string $giftcardImageType
     */
    public function __construct(
        GiftcardHelperFactory $giftcardHelperFactory,
        GiftcardQuoteItem $giftcardQuoteItem,
        $giftcardImageType = null
    ) {
        $this->giftcardHelperFactory = $giftcardHelperFactory;
        $this->giftcardQuoteItem = $giftcardQuoteItem;
        $this->giftcardImageType = $giftcardImageType;
    }

    /**
     * @param \Magento\Checkout\CustomerData\DefaultItem $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGetItemData(\Magento\Checkout\CustomerData\DefaultItem $subject, $result)
    {
        if (isset($result['item_id']) && $result['product_type'] == Giftcard::TYPE_GIFTCARD) {
            $giftCardImage = $this->giftcardQuoteItem->getGiftcardImageByQuoteItemId($result['item_id']);
            if ($giftCardImage) {
                $attributes = [];
                if ($this->giftcardImageType) {
                    $attributes['type'] = $this->giftcardImageType;
                }
                $imageHelper = $this->giftcardHelperFactory->create();
                $imageHelper->init($giftCardImage, GiftCardImageInterface::IMAGE, $attributes);
                $result['product_image']['src'] = $imageHelper->getUrl();
            }
        }
        return $result;
    }
}
