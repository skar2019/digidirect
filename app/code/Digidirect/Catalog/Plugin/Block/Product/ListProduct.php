<?php

namespace Digidirect\Catalog\Plugin\Block\Product;

class ListProduct
{
    protected $logger;
    
    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface
    ){
        $this->logger = $loggerInterface;
    }
    
    public function afterGetProductCollection(\Magento\Catalog\Model\Layer $subject, $result) {
        $this->logger->info('afterGetProductCollection');
        //$result->addAttributeToSort('brand','ASC'); //DESC for descending.
        //$result->addAttributeToSort('sku');
        //$result->setOrder('brand','ASC'); For dropdown type attribute you can use this.
        return $result;
    }
}