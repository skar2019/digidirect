<?php

namespace Digidirect\MyOrderItemsGroups\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface OrderItemGroupInterface
 * @package Digidirect\MyOrderItemsGroups\Api\Data
 */
interface OrderItemGroupInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constant for order item group
     */
    const GROUP_ID = 'group_id';
    const NAME = 'name';
    const CUSTOMER_ID = 'customer_id';
    const UPDATED_AT = 'updated_at';
    /**#@-*/

    /**
     * @return int
     */
    public function getGroupId();

    /**
     * @param int $groupId
     * @return OrderItemGroupInterface
     */
    public function setGroupId($groupId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return OrderItemGroupInterface
     */
    public function setName($name);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     * @return OrderItemGroupInterface
     */
    public function setCustomerId($customerId);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     * @return OrderItemGroupInterface
     */
    public function setUpdatedAt($updatedAt);
}
