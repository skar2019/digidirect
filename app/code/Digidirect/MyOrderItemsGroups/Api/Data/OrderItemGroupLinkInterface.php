<?php

namespace Digidirect\MyOrderItemsGroups\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface OrderItemGroupLinkInterface
 * @package Digidirect\MyOrderItemsGroups\Api\Data
 */
interface OrderItemGroupLinkInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constant for order item group link
     */
    const LINK_ID = 'link_id';
    const GROUP_ID = 'group_id';
    const SALES_ITEM_ID = 'sales_item_id';
    const POSITION = 'position';
    /**#@-*/

    /**
     * @return int
     */
    public function getLinkId();

    /**
     * @param int $linkId
     * @return OrderItemGroupLinkInterface
     */
    public function setLinkId($linkId);

    /**
     * @return string
     */
    public function getGroupId();

    /**
     * @param int $groupId
     * @return OrderItemGroupLinkInterface
     */
    public function setGroupId($groupId);

    /**
     * @return int
     */
    public function getSalesItemId();

    /**
     * @param int $salesItemId
     * @return OrderItemGroupLinkInterface
     */
    public function setSalesItemId($salesItemId);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     * @return OrderItemGroupLinkInterface
     */
    public function setPosition($position);
}
