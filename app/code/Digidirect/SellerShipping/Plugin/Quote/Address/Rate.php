<?php

namespace Digidirect\SellerShipping\Plugin;

class Rate
{
    
    public function afterImportShippingRate(\Magento\Quote\Model\Quote\Address\Rate $subject, $result, $rate)
    {
        if ($rate instanceof \Magento\Quote\Model\Quote\Address\RateResult\Method) {
            if($result->getCode() == 'standard') {
                $result->setCode('standard')->setPrice(50);
            }
        }
        return $result;
    }

}
