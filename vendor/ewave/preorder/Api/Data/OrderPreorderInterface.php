<?php

namespace Ewave\PreOrder\Api\Data;

/**
 * @api
 */
interface OrderPreorderInterface
{
    /**#@-
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const ORDER_ID = 'order_id';
    const IS_PREORDER = 'is_preorder';
    const WARNING = 'warning';
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
     * Get order_id
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Set order_id
     *
     * @param int $orderId
     * @return $this
     */
    public function setOrderId($orderId);

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

    /**
     * Get warning
     *
     * @return string|null
     */
    public function getWarning();

    /**
     * Set warning
     *
     * @param string $warning
     * @return $this
     */
    public function setWarning($warning);
}
