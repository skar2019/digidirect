<?php

namespace Marketplacer\SellerApi\Model;

use Magento\Framework\Model\AbstractModel;
use Marketplacer\SellerApi\Api\Data\MarketplacerSellerInterface;

class MarketplacerSeller extends AbstractModel implements MarketplacerSellerInterface
{
    /**
     * Get seller identifier
     *
     * @return int | string | null
     */
    public function getSellerId()
    {
        return $this->_getData(MarketplacerSellerInterface::SELLER_ID);
    }

    /**
     * Set seller identifier
     *
     * @param int $sellerId
     * @return self | MarketplacerSellerInterface
     */
    public function setSellerId($sellerId)
    {
        $this->setData(MarketplacerSellerInterface::SELLER_ID, $sellerId);
        return $this;
    }

    /**
     * Get Seller name
     *
     * @return int|string|null
     */
    public function getName()
    {
        return $this->_getData(MarketplacerSellerInterface::NAME);
    }

    /**
     * @param string $name
     * @return self|MarketplacerSellerInterface
     */
    public function setName($name)
    {
        $this->setData(MarketplacerSellerInterface::NAME, $name);
        return $this;
    }

    /**
     * Get seller logo url
     *
     * @return int|string|null
     */
    public function getLogo()
    {
        return $this->_getData(MarketplacerSellerInterface::LOGO);
    }

    /**
     * Set seller logo url
     *
     * @param string|null $logoUrl
     * @return $this|MarketplacerSellerInterface
     */
    public function setLogo($logoUrl)
    {
        $this->setData(MarketplacerSellerInterface::LOGO, $logoUrl);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getStoreImage()
    {
        return $this->_getData(MarketplacerSellerInterface::STORE_IMAGE);
    }

    /**
     * @param string $imageUrl
     * @return MarketplacerSellerInterface
     */
    public function setStoreImage($imageUrl)
    {
        $this->setData(MarketplacerSellerInterface::STORE_IMAGE, $imageUrl);
        return $this;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->_getData(MarketplacerSellerInterface::EMAIL_ADDRESS);
    }

    /**
     * @param string $email
     * @return MarketplacerSellerInterface
     */
    public function setEmailAddress($email)
    {
        $this->setData(MarketplacerSellerInterface::EMAIL_ADDRESS, $email);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPhone()
    {
        return $this->_getData(MarketplacerSellerInterface::PHONE);
    }

    /**
     * @param string $phone
     * @return MarketplacerSellerInterface
     */
    public function setPhone($phone)
    {
        $this->setData(MarketplacerSellerInterface::PHONE, $phone);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAddress()
    {
        return $this->_getData(MarketplacerSellerInterface::ADDRESS);
    }

    /**
     * @param string $address
     * @return MarketplacerSellerInterface
     */
    public function setAddress($address)
    {
        $this->setData(MarketplacerSellerInterface::ADDRESS, $address);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->_getData(MarketplacerSellerInterface::DESCRIPTION);
    }

    /**
     * @param string $description
     * @return MarketplacerSellerInterface
     */
    public function setDescription($description)
    {
        $this->setData(MarketplacerSellerInterface::DESCRIPTION, $description);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOpeningHours()
    {
        return $this->_getData(MarketplacerSellerInterface::OPENING_HOURS);
    }

    /**
     * @param string $openHours
     * @return MarketplacerSellerInterface
     */
    public function setOpeningHours($openHours)
    {
        $this->setData(MarketplacerSellerInterface::OPENING_HOURS, $openHours);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBusinessNumber()
    {
        return $this->_getData(MarketplacerSellerInterface::BUSINESS_NUMBER);
    }

    /**
     * @param string $businessNumber
     * @return MarketplacerSellerInterface
     */
    public function setBusinessNumber($businessNumber)
    {
        $this->setData(MarketplacerSellerInterface::BUSINESS_NUMBER, $businessNumber);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPolicies()
    {
        return $this->_getData(MarketplacerSellerInterface::POLICIES);
    }

    /**
     * @param string $policies
     * @return MarketplacerSellerInterface
     */
    public function setPolicies($policies)
    {
        $this->setData(MarketplacerSellerInterface::POLICIES, $policies);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getShippingPolicy()
    {
        return $this->_getData(MarketplacerSellerInterface::SHIPPING_POLICY);
    }

    /**
     * @param string $shippingPolicy
     * @return MarketplacerSellerInterface
     */
    public function setShippingPolicy($shippingPolicy)
    {
        $this->setData(MarketplacerSellerInterface::SHIPPING_POLICY, $shippingPolicy);
        return $this;
    }
}
