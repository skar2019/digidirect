<?php

namespace Ewave\Newsletter\Api\Data;

/**
 * Interface SubscriberInterface
 * @package Ewave\Newsletter\Api\Data
 */
interface SubscriberInterface
{
    const SUBSCRIBER_ID = 'subscriber_id';
    const FIRSTNAME = 'firstname';
    const LASTNAME = 'lastname';

    /**
     * @param int $subscriberId
     * @return $this
     */
    public function setSubscriberId($subscriberId);

    /**
     * @return int|null
     */
    public function getSubscriberId();

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName($firstName);

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @param string $lastName
     * @return $this
     */
    public function setLastName($lastName);

    /**
     * @return string
     */
    public function getLastName();
}
