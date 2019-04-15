<?php

namespace Ewave\Newsletter\Api;

use Ewave\Newsletter\Api\Data\SubscriberInterface;
use Ewave\Newsletter\Model\Subscriber;

/**
 * Interface SubscriberRepositoryInterface
 * @package Ewave\Newsletter\Api
 */
interface SubscriberRepositoryInterface
{
    /**
     * @param int $subscriberId
     * @return SubscriberInterface
     */
    public function getBySubscriberId($subscriberId);

    /**
     * @param Subscriber $subscriber
     * @return Subscriber
     */
    public function save(Subscriber $subscriber);
}
