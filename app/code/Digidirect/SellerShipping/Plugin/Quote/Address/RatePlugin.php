<?php

namespace Digidirect\SellerShipping\Plugin\Quote\Address;

class RatePlugin
{
    protected $helperData;
    
    protected $logger;
    
    
    public function __construct(
        \Digidirect\SellerShipping\Helper\Data $helperData,
        \Psr\Log\LoggerInterface $logger
    ){
        $this->helperData = $helperData;
        $this->logger = $logger;
    }
    
    public function afterImportShippingRate(\Magento\Quote\Model\Quote\Address\Rate $subject, $result, $rate)
    {
        $this->logger->info('afterImportShippingRate');
        if ($rate instanceof \Magento\Quote\Model\Quote\Address\RateResult\Method) {
            $result->setPrice($result->getPrice() + $this->helperData->getSellerShipping());
        }
        return $result;
    }

}
