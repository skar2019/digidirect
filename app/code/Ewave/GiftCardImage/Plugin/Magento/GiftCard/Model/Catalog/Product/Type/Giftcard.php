<?php
namespace Ewave\GiftCardImage\Plugin\Magento\GiftCard\Model\Catalog\Product\Type;

use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;

class Giftcard
{
    /**
     * @param \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard $subject
     * @param \Closure $proceed $subject
     * @param  \Magento\Catalog\Model\Product $product
     * @param  \Magento\Framework\DataObject $buyRequest
     * @return array
     */
    public function aroundProcessBuyRequest(
        \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard $subject,
        \Closure $proceed,
        $product,
        $buyRequest
    ) {
        $options = $proceed($product, $buyRequest);
        $options[GiftCardImageInterface::PUBLIC_KEY] = $buyRequest->getData(GiftCardImageInterface::PUBLIC_KEY);
        return $options;
    }
}
