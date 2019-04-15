<?php

namespace Ewave\Newsletter\Plugin\Newsletter\Model\ResourceModel\Subscriber;

use Ewave\Newsletter\Api\Data\SubscriberInterface;
use Ewave\Newsletter\Model\ResourceModel\Subscriber as SubscriberResource;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class CollectionPlugin
 * @package Ewave\Newsletter\Plugin\Newsletter\Model\ResourceModel\Subscriber
 */
class CollectionPlugin
{
    /**
     * @param \Magento\Newsletter\Model\ResourceModel\Subscriber\Collection $subject
     * @param \Closure $closure
     * @return \Magento\Newsletter\Model\ResourceModel\Subscriber\Collection
     */
    public function aroundShowCustomerInfo(
        \Magento\Newsletter\Model\ResourceModel\Subscriber\Collection $subject,
        \Closure $closure
    ) {
        $subject->getSelect()->joinLeft(
            [
                'customer' => $subject->getTable('customer_entity')
            ],
            'main_table.customer_id = customer.entity_id',
            []
        )->joinLeft(
            [
                'subscirber' => $subject->getTable(SubscriberResource::TABLE)
            ],
            'main_table.subscriber_id = subscirber.' . SubscriberInterface::SUBSCRIBER_ID,
            [
                'firstname' => new \Zend_Db_Expr(
                    'IFNULL(subscirber.'
                    . SubscriberInterface::FIRSTNAME .', customer.' . CustomerInterface::FIRSTNAME . ')'
                ),
                'lastname' => new \Zend_Db_Expr(
                    'IFNULL(subscirber.'
                    . SubscriberInterface::LASTNAME .', customer.' . CustomerInterface::LASTNAME . ')'
                )
            ]
        );
        return $subject;
    }
}
