<?php

namespace Digidirect\Catalog\Plugin\Model\Product;

class Url
{
    private $storeManager;
    
    protected $logger;
    
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,    
    ){
        $this->storeManager = $storeManager;  
        $this->logger = $logger; 
    }

    public function afterGetProductUrl(
        \Magento\Catalog\Model\Product\Url $subject,
        $result,
        $product, 
        $routeParams    
    ) {
        $storeId = $product->getStoreId();
         if (isset($routeParams['_scope'])) {
            $storeId = $this->storeManager->getStore($routeParams['_scope'])->getId();
        }
        
        $urlComponents = parse_url($result);
        $this->logger->info('$result: ' . $result);
        $this->logger->info('$urlComponents[path]: ' . $urlComponents['path']);
        
        $dir = explode('/', $urlComponents['path']);
        $lastDir = end($dir);
        $this->logger->info('$lastDir: ' . $lastDir);
        
        return $lastDir;
    }
}