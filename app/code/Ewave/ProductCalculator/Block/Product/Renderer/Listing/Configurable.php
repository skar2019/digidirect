<?php
namespace Ewave\ProductCalculator\Block\Product\Renderer\Listing;

use Magento\Catalog\Api\Data\ProductInterface;

class Configurable extends \Magento\Swatches\Block\Product\Renderer\Listing\Configurable
{
    /**
     * @var array
     */
    protected $renderedProducts = [];

    /**
     * @param ProductInterface $product
     * @return $this
     */
    public function saveRenderedProduct(ProductInterface $product)
    {
        $this->renderedProducts[$product->getId()] = 1;
        return $this;
    }

    /**
     * @param ProductInterface $product
     * @return integer
     */
    public function isProductRendered(ProductInterface $product)
    {
        return $this->renderedProducts[$product->getId()] ?? null;
    }
}
