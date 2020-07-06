<?php

namespace Ewave\ProntoDigi\ProntoApi\Inventory\Get;

/**
 * Class MapperHelper
 * @package Ewave\ProntoDigi\ProntoApi\Inventory\Get
 */
class MapperHelper
{
    /**
     * @param int|null $price
     * @return int
     */
    public function getPrice($price)
    {
        return !empty($price) ? $price : 0;
    }
}
