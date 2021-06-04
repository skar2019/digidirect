<?php

namespace Ewave\Newsletter\Model;

use Ewave\Newsletter\Api\SubscriberRepositoryInterface;
use Ewave\Newsletter\Model\ResourceModel\Subscriber as SubscriberResource;
use Magento\Framework\Exception\CouldNotSaveException;

class SubscriberRepository implements SubscriberRepositoryInterface
{
    /**
     * @var SubscriberResource
     */
    protected $subscriberResource;

    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * SubscriberRepository constructor.
     * @param SubscriberResource $subscriberResource
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(SubscriberResource $subscriberResource, SubscriberFactory $subscriberFactory)
    {
        $this->subscriberResource = $subscriberResource;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * @param int $subscriberId
     * @return \Ewave\Newsletter\Api\Data\SubscriberInterface|mixed
     * @throws NoSuchEntityException
     */
    public function getBySubscriberId($subscriberId)
    {
        $subscriber = $this->subscriberFactory->create();
        $this->subscriberResource->load($subscriber, $subscriberId);
        return $subscriber;
    }

    /**
     * @param Subscriber $subscriber
     * @return Subscriber
     */
    public function save(Subscriber $subscriber)
    {
        try {
            $this->subscriberResource->save($subscriber);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the subscription storefront fields for subsriber ID: %1, Error message %2',
                $subscriber->getId(),
                $exception->getMessage()
            ));
        }

        return $subscriber;
    }
}
