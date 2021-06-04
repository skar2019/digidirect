<?php

namespace Ewave\ExtendedShippingRatesShippingAvailability\Api;

/**
 * Interface ZoneCheckManagementInterface
 * @package Ewave\ExtendedShippingRatesShippingAvailability\Api
 */
interface ZoneCheckManagementInterface
{
    /**
     * @param string $postcode
     * @return \Ewave\ExtendedShippingRates\Api\Data\ZoneInterface;
     */
    public function getZoneByPostcode($postcode);
}
