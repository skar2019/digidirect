<?php
namespace Ewave\CollectStaging\Model;

use Ewave\Collect\Api\CollectPlaceAddToCartValidationInterface;
use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;

/**
 * Class CollectPlaceAddToCartValidation
 * @package Ewave\CollectStaging\Model
 */
class CollectPlaceAddToCartValidation implements CollectPlaceAddToCartValidationInterface
{
    /**
     * @param QuoteItem $quoteItem
     * @param Quote $quote
     * @param Product $product
     * @param array $requestInfo
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isProductAvailableInPreviouslySelectedStore(
        QuoteItem $quoteItem,
        Quote $quote,
        Product $product,
        array $requestInfo = []
    ) {
        return $product->getId() % 2 === 0;
    }
}
