<?php
namespace Ewave\Localization\Api\Data;

/**
 * Interface LengthUnitInterface
 * @package Ewave\Localization\Api\Data
 */
interface LengthUnitInterface
{
    /**
     * @param float $miles
     * @param int $precision
     * @return float
     */
    public function convertMilesToUnit($miles, $precision);
}
