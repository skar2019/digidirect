<?php

namespace Digidirect\Catalog\Plugin;

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
    
    public function afterGet($subject, $result) 
    {
        // Modify SKU before returning the result
        $this->logger->info('Test Product API Override!');
        $result->setSku(substr($result->getSku(), 0, 3));

        return $result;
    }
    
}
