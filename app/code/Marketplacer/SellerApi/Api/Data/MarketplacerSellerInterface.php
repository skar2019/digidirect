<?php

namespace Marketplacer\SellerApi\Api\Data;

use Marketplacer\Base\Api\Data\DataObjectInterface;

interface MarketplacerSellerInterface extends DataObjectInterface
{
    public const SELLER_ID = 'seller_id';
    public const NAME = 'name';
    public const LOGO = 'logo';
    public const STORE_IMAGE = 'store_image';
    public const PHONE = 'phone';
    public const ADDRESS = 'address';
    public const DESCRIPTION = 'description';
    public const OPENING_HOURS = 'opening_hours';
    public const BUSINESS_NUMBER = 'business_number';
    public const POLICIES = 'policies';
    public const SHIPPING_POLICY = 'shipping_policy';
    public const EMAIL_ADDRESS = 'email_address';

    /**
     * Get Seller ID
     *
     * @return int | null
     */
    public function getSellerId();

    /**
     * @param int $sellerId
     * @return MarketplacerSellerInterface
     */
    public function setSellerId($sellerId);

    /**
     * Get name
     *
     * @return string | null
     */
    public function getName();

    /**
     * @param string | null $name
     * @return MarketplacerSellerInterface
     */
    public function setName($name);

    /**
     * Get logo image
     *
     * @return string | null
     */
    public function getLogo();

    /**
     * @param string | null $imageUrl
     * @return MarketplacerSellerInterface
     */
    public function setLogo($imageUrl);

    /**
     * @return string | null
     */
    public function getStoreImage();

    /**
     * @param string | null $imageUrl
     * @return MarketplacerSellerInterface
     */
    public function setStoreImage($imageUrl);

    /**
     * @return string | null
     */
    public function getEmailAddress();

    /**
     * @param string | null $email
     * @return MarketplacerSellerInterface
     */
    public function setEmailAddress($email);

    /**
     * @return string | null
     */
    public function getPhone();

    /**
     * @param string | null $phone
     * @return MarketplacerSellerInterface
     */
    public function setPhone($phone);

    /**
     * @return string | null
     */
    public function getAddress();

    /**
     * @param string | null $address
     * @return MarketplacerSellerInterface
     */
    public function setAddress($address);

    /**
     * @return string | null
     */
    public function getDescription();

    /**
     * @param string | null $description
     * @return MarketplacerSellerInterface
     */
    public function setDescription($description);

    /**
     * @return string | null
     */
    public function getOpeningHours();

    /**
     * @param string | null $openHours
     * @return MarketplacerSellerInterface
     */
    public function setOpeningHours($openHours);

    /**
     * @return string | null
     */
    public function getBusinessNumber();

    /**
     * @param string | null $businessNumber
     * @return MarketplacerSellerInterface
     */
    public function setBusinessNumber($businessNumber);

    /**
     * @return string | null
     */
    public function getPolicies();

    /**
     * @param string | null $policies
     * @return MarketplacerSellerInterface
     */
    public function setPolicies($policies);

    /**
     * @return string | null
     */
    public function getShippingPolicy();

    /**
     * @param string | null $shippingPolicy
     * @return MarketplacerSellerInterface
     */
    public function setShippingPolicy($shippingPolicy);
}
