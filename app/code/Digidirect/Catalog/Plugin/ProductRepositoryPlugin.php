<?php

namespace Digidirect\Catalog\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;

class ProductRepositoryPlugin
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
    
    public function afterGet
    (
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Magento\Catalog\Api\Data\ProductInterface $entity
    ) {
        // Modify SKU before returning the result
        $this->logger->info('Test Product API Override!');
        $entity->setSku(substr($entity->getSku(), 0, 3));

        return $entity;
    }
    
}
