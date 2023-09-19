<?php

namespace Onsport\Shipping\Plugin\Quote\Api;

use Magento\Quote\Api\ShipmentEstimationInterface;
use Magento\Checkout\Model\Cart;

class ShipmentEstimationPlugin
{
    public function __construct(
        Cart $cart
    ) {
        $this->cart = $cart;
    }

    public function aroundEstimateByExtendedAddress (
        ShipmentEstimationInterface $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Quote\Api\Data\AddressInterface $address
    ) {
        $shippingMethods = $proceed($cartId, $address);
        $subTotal = $this->cart->getQuote()->getSubtotal();
        
        foreach ($shippingMethods as $key => $shippingMethod) {
            if ($shippingMethod->getMethodCode() == 'flatrate' && $subTotal >= 99) {
                unset($shippingMethods[$key]);
            }
        }
        return $shippingMethods;
    }
}