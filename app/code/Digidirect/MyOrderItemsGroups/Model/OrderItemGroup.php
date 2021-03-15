<?php

namespace Digidirect\MyOrderItemsGroups\Model;

use Magento\Framework\Model\AbstractModel;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupInterface;

/**
 * Class OrderItemGroup
 * @package Digidirect\MyOrderItemsGroups\Model
 */
class OrderItemGroup extends AbstractModel implements OrderItemGroupInterface
{
    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Digidirect\MyOrderItemsGroups\Model\ResourceModel\OrderItemGroup::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupId()
    {
        return $this->getData(self::GROUP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setGroupId($groupId)
    {
        return $this->setData(self::GROUP_ID, $groupId);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
