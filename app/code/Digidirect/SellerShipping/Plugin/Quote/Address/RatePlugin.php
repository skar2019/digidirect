<?php

declare(strict_types=1);

namespace Digidirect\SellerShipping\Plugin\Quote\Address;

use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Quote\Model\Quote\Address\RateResult\AbstractResult;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

class RatePlugin
{
    protected $logger;
    
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ){
        $this->logger = $logger;
    }
    
    public function afterImportShippingRate(Rate $subject, Rate $result, AbstractResult $rate): Rate {
        $this->logger->info('afterImportShippingRate');
        if ($rate instanceof Method) {
            $result->setPrice($result->getPrice() + 20);
        }
        return $result;
    }

}
