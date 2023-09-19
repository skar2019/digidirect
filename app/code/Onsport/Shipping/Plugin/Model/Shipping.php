<?php

namespace Onsport\Shipping\Plugin\Model;

use Magento\Checkout\Model\Cart;

class Shipping {
    
    protected $cart;
    
    public function __construct(
        Cart $cart
    ){
        $this->cart = $cart;
    }
       
    public function aroundCollectCarrierRates (
        \Magento\Shipping\Model\Shipping $subject,
        \Closure $proceed,
        $carrierCode,
        $request
    ) {
        $rateRequest = $this->rateRequestFactory->create();
        $subTotal = $rateRequest->setPackageValue($address->getBaseSubtotal());
        
        if ($carrierCode == 'flatrate' && $subTotal >= 99) {
            return false;
        }
        return $proceed($carrierCode, $request);
    }
   
}