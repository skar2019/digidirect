<?php

namespace Digidirect\Catalog\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class ProductRepository
{
    
    protected $logger;
    /**
     * @param ProductRepositoryInterface $subject
     * @param ProductInterface $result
     * @return ProductInterface
     */
    
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }
    
    public function afterGet(
        ProductRepositoryInterface $subject,
        ProductInterface $result
    ) {
        // Modify SKU before returning the result
        $this->logger->info('Test Product API Override!');
        $result->setSku(substr($result->getSku(), 0, 3));

        return $result;
    }
}
