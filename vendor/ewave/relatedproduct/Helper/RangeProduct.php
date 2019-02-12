<?php
namespace Ewave\RelatedProduct\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Model\Product;

/**
 * Class RangeProduct
 * @author Ewave team
 * @package Ewave\RelatedProduct\Helper
 */
class RangeProduct extends AbstractHelper
{
    const XML_PATH_RELATED_PRODUCT_SHOWN_ATTRIBUTE_SETS = 'ewave_related_product/shown_parameters/attribute_sets';

    /**
     * @return array
     */
    public function getRelatedProductImagesRanges()
    {
        return explode(',', $this->scopeConfig->getValue(self::XML_PATH_RELATED_PRODUCT_SHOWN_ATTRIBUTE_SETS));
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function isRangeProduct(Product $product)
    {
        return in_array($product->getAttributeSetId(), $this->getRelatedProductImagesRanges());
    }
}
