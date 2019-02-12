<?php
namespace Ewave\AddressVerification\Model\CountryAddress\Source;

/**
 * Interface SourceProviderInterface
 * @package Ewave\AddressVerification\Model\CountryAddress
 */
interface SourceProviderInterface
{
    /**
     * @return array
     */
    public function getOptions();
}
