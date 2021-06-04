<?php
namespace Ewave\Collect\Api;

use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;

/**
 * Interface CollectPlaceAddToCartValidation
 * @package Ewave\Collect\Api
 */
interface CollectPlaceAddToCartValidationInterface
{
    /**
     * @param QuoteItem $quoteItem
     * @param Quote $quote
     * @param Product $product
     * @param array $requestInfo
     * @return bool
     */
    public function isProductAvailableInPreviouslySelectedStore(
        QuoteItem $quoteItem,
        Quote $quote,
        Product $product,
        array $requestInfo = []
    );
}
