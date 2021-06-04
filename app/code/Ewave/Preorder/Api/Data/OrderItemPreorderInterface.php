<?php

namespace Ewave\PreOrder\Api\Data;

/**
 * @api
 */
interface OrderItemPreorderInterface
{
    /**#@-
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const ORDER_ITEM_ID = 'order_item_id';
    const IS_PREORDER = 'is_preorder';
    /**#@-*/

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get order_item_id
     *
     * @return int|null
     */
    public function getOrderItemId();

    /**
     * Set order_item_id
     *
     * @param int $orderItemId
     * @return $this
     */
    public function setOrderItemId($orderItemId);

    /**
     * Get is_preorder
     *
     * @return bool|null
     */
    public function getIsPreorder();

    /**
     * Set is_preorder
     *
     * @param bool $isPreorder
     * @return $this
     */
    public function setIsPreorder($isPreorder);
}
