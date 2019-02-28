<?php
namespace Ewave\Localization\Model\Convert;

use Ewave\Localization\Api\Data\LengthUnitInterface;

/**
 * Class Kilometer
 * @package Ewave\Localization\Model\Convert
 */
class Kilometer implements LengthUnitInterface
{
    const KILOMETERS_IN_MILE = 1.609344;

    /**
     * @param float $miles
     * @param int $precision
     * @return float
     */
    public function convertMilesToUnit($miles, $precision)
    {
        return round($miles * self::KILOMETERS_IN_MILE, $precision);
    }
}
