<?php
namespace Ewave\Digi\Api\Rest;

/**
 * Interface CheckSsdAvailabilityInterface
 * @package Ewave\Digi\Api\Rest
 */
interface CheckVsmAvailabilityInterface
{
    /**
     * @param string $country
     * @param string $postCode
     * @return mixed
     */
    public function getByCountryAndPostCode(string $country, string $postCode);
}
