<?php
namespace Ewave\Collect\Api;

/**
 * Interface DeliveryAddToCartValidation
 * @package Ewave\Collect\Api
 */
interface DeliveryAddToCartValidationInterface
{
    /**
     * @return string
     */
    public function hasDeliveryItemInCart();
}
