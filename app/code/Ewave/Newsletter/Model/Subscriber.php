<?php

namespace Ewave\Newsletter\Model;

use Ewave\Newsletter\Api\Data\SubscriberInterface;
use Ewave\Newsletter\Model\ResourceModel\Subscriber as SubscriberResource;

/**
 * Class Subscriber
 * @package Ewave\Newsletter\Model
 */
class Subscriber extends \Magento\Framework\Model\AbstractModel implements SubscriberInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SubscriberResource::class);
    }

    /**
     * @param int $subscriberId
     * @return $this
     */
    public function setSubscriberId($subscriberId)
    {
        $this->setData(self::SUBSCRIBER_ID, $subscriberId);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSubscriberId()
    {
        return $this->getData(self::SUBSCRIBER_ID);
    }

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->setData(self::FIRSTNAME, $firstName);
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->getData(self::FIRSTNAME);
    }

    /**
     * @param string $lastName
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->setData(self::LASTNAME, $lastName);
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->getData(self::LASTNAME);
    }
}
