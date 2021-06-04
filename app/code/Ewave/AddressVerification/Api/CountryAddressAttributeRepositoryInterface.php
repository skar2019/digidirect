<?php
namespace Ewave\AddressVerification\Api;

/**
 * Interface CountryAddressAttributeRepositoryInterface
 * @package Ewave\AddressVerification\Api
 */
interface CountryAddressAttributeRepositoryInterface
{
    /**
     * @param int $id
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function getById($id);

    /**
     * @param int $id
     * @return $this
     */
    public function deleteById($id);

    /**
     * @param \Ewave\AddressVerification\Model\CountryAddressAttribute $item
     * @return \Ewave\AddressVerification\Model\CountryAddressAttribute
     */
    public function save(\Ewave\AddressVerification\Model\CountryAddressAttribute $item);

    /**
     * @param string $code
     * @return \Ewave\AddressVerification\Model\CountryAddressAttribute
     */
    public function getByCountryCode($code);

    /**
     * @param string $countryCode
     * @param bool $decode
     * @return array
     */
    public function getAddressAttributesByCountryCode($countryCode, $decode = true);

    /**
     * @return \Ewave\AddressVerification\Model\ResourceModel\CountryAddressAttribute\Collection
     */
    public function getAll();
}
