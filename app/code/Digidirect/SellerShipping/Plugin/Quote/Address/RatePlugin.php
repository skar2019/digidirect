<?php

namespace Digidirect\SellerShipping\Plugin\Quote\Address;

class RatePlugin
{
    protected $logger;
    
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ){
        $this->logger = $logger;
    }
    
    public function afterImportShippingRate($subject, $result, $rate)
    {
        $this->logger->info('afterImportShippingRate');
        if ($rate instanceof \Magento\Quote\Model\Quote\Address\RateResult\Method) {
            $result->setPrice($result->getPrice() + 20);
        }
        return $result;
    }

}
