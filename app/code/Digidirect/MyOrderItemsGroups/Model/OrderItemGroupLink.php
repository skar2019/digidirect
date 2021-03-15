<?php

namespace Digidirect\MyOrderItemsGroups\Model;

use Magento\Framework\Model\AbstractModel;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupLinkInterface;

/**
 * Class OrderItemGroup
 * @package Digidirect\MyOrderItemsGroups\Model
 */
class OrderItemGroupLink extends AbstractModel implements OrderItemGroupLinkInterface
{
    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Digidirect\MyOrderItemsGroups\Model\ResourceModel\OrderItemGroupLink::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkId()
    {
        return $this->getData(self::LINK_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setLinkId($linkId)
    {
        return $this->setData(self::LINK_ID, $linkId);
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
    public function getSalesItemId()
    {
        return $this->getData(self::SALES_ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSalesItemId($salesItemId)
    {
        return $this->setData(self::SALES_ITEM_ID, $salesItemId);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }
}
