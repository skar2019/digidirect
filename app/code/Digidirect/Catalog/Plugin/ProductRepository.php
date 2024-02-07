<?php

namespace Digidirect\Catalog\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class ProductRepository
{
    /**
     * @param ProductRepositoryInterface $subject
     * @param ProductInterface $result
     * @return ProductInterface
     */
    public function afterGet(
        ProductRepositoryInterface $subject,
        ProductInterface $result
    ) {
        // Modify SKU before returning the result
        $result->setSku(substr($result->getSku(), 0, 50));

        return $result;
    }
}
