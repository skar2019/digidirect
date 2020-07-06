<?php

namespace Ewave\CollectAbstractEntityMSI\Api\Data;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface ProductTypeHandlerInterface
 * @package Ewave\CollectAbstractEntityMSI\Api
 */
interface ProductTypeHandlerInterface
{
    /**
     * @param ProductInterface $product
     * @param array $options
     * @return array
     */
    public function process(ProductInterface $product, array $options);

    /**
     * Product type id
     *
     * @return string
     */
    public function getTypeId();
}
