<?php

namespace Onsport\Shipping\Plugin\Model;

use Magento\Checkout\Model\Cart;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateRequestFactory;

class Shipping {
    
    protected $cart;
    
    protected $rateRequestFactory;
    
    public function __construct(
        Cart $cart,
        RateRequestFactory $rateRequestFactory = null
    ){
        $this->cart = $cart;
        $this->rateRequestFactory = $rateRequestFactory ?: ObjectManager::getInstance()->get(RateRequestFactory::class);
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