<?php
namespace Ewave\Collect\Api;

/**
 * Interface CollectAddToCartValidationInterface
 * @package Ewave\Collect\Api
 */
interface CollectAddToCartValidationInterface
{
    /**
     * @return string
     */
    public function hasCollectItemInCart();
}
