<?php

namespace Digidirect\Checkout\Plugin\Model;

class Shipping {
    
    public function aroundCollectCarrierRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Closure $proceed,
        $carrierCode,
        $request
    ) {
        if ($carrierCode == 'standard') {
            return false;
        }
        return $proceed($carrierCode, $request);
    }
}