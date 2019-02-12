<?php
namespace Ewave\GiftCardImage\Plugin\Magento\Checkout\Block\Cart\Item;

use Ewave\GiftCardImage\Block\Catalog\Product\ImageBuilder;

class Renderer
{
    /**
     * @param \Magento\Checkout\Block\Cart\Item\Renderer $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGetImage(\Magento\Checkout\Block\Cart\Item\Renderer $subject, $result)
    {
        if ($result instanceof ImageBuilder) {
            return $result->setQuoteItem($subject->getItem())
                ->create();
        }
        return $result;
    }
}
