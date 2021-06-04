<?php
namespace Ewave\ExtendedShippingRates\Api\Data;

interface CarrierInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const CARRIER_ID = 'carrier_id';
    const CARRIER_CODE = 'carrier_code';
    const ACTIVE = 'active';
    const SALLOWSPECIFIC = 'sallowspecific';
    const MODEL = 'model';
    const NAME = 'name';
    const TITLE = 'title';
    const TYPE = 'type';
    const SPECIFICERRMSG = 'specificerrmsg';
    const PRICE = 'price';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const METHODS = 'methods';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get carrier code
     *
     * @return string|null
     */
    public function getCarrierCode();

    /**
     * Get sallowspecific
     *
     * @return string|null
     */
    public function getSallowspecific();

    /**
     * Get model
     *
     * @return string|null
     */
    public function getModel();

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get type
     *
     * @return string|null
     */
    public function getType();

    /**
     * Get specificerrmsg
     *
     * @return string|null
     */
    public function getSpecificerrmsg();

    /**
     * Get price
     *
     * @return string|null
     */
    public function getPrice();

    /**
     * Get created at
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get updated at
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Get methods
     *
     * @return array|null
     */
    public function getMethods();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Ewave\ExtendedShippingRates\Api\Data\CarrierInterface
     */
    public function setId($id);

    /**
     * Set carrier code
     *
     * @param string $carrierCode
     * @return \Ewave\ExtendedShippingRates\Api\Data\CarrierInterface
     */
    public function setCarrierCode($carrierCode);

    /**
     * Set sallowspecific
     *
     * @param string $sallowspecific
     * @return \Ewave\ExtendedShippingRates\Api\Data\CarrierInterface
     */
    public function setSallowspecific($sallowspecific);

    /**
     * Set model
     *
     * @param string $model
     * @return \Ewave\ExtendedShippingRates\Api\Data\CarrierInterface
     */
    public function setModel($model);

    /**
     * Set name
     *
     * @param string $name
     * @return \Ewave\ExtendedShippingRates\Api\Data\CarrierInterface
     */
    public function setName($name);

    /**
     * Set title
     *
     * @param string $title
     * @return \Ewave\ExtendedShippingRates\Api\Data\CarrierInterface
     */
    public function setTitle($title);

    /**
     * Set type
     *
     * @param string $type
     * @return \Ewave\ExtendedShippingRates\Api\Data\CarrierInterface
     */
    public function setType($type);

    /**
     * Set specificerrmsg
     *
     * @param string $specificerrmsg
     * @return \Ewave\ExtendedShippingRates\Api\Data\CarrierInterface
     */
    public function setSpecificerrmsg($specificerrmsg);

    /**
     * Set price
     *
     * @param string $price
     * @return \Ewave\ExtendedShippingRates\Api\Data\CarrierInterface
     */
    public function setPrice($price);

    /**
     * Set created at
     *
     * @param string $createdAt
     * @return \Ewave\ExtendedShippingRates\Api\Data\CarrierInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Set updated at
     *
     * @param string $updatedAt
     * @return \Ewave\ExtendedShippingRates\Api\Data\CarrierInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Set methods
     *
     * @param array $methods
     * @return \Ewave\ExtendedShippingRates\Api\Data\CarrierInterface
     */
    public function setMethods($methods);

    /**
     * Set active
     *
     * @param int|bool $active
     * @return \Ewave\ExtendedShippingRates\Api\Data\CarrierInterface
     */
    public function setActive($active);
}
