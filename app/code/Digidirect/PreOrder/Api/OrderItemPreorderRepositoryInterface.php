<?php

namespace Digidirect\PreOrder\Api;

/**
 * OrderItemPreorder CRUD interface.
 *
 * @api
 */
interface OrderItemPreorderRepositoryInterface
{
    /**
     * Create or update an order item PreOrder.
     *
     * @param \Digidirect\PreOrder\Api\Data\OrderItemPreorderInterface $orderItemPreorder
     * @return \Digidirect\PreOrder\Api\Data\OrderItemPreorderInterface
     * @throws \Magento\Framework\Exception\InputException If bad input is provided
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Digidirect\PreOrder\Api\Data\OrderItemPreorderInterface $orderItemPreorder);

    /**
     * Get order item PreOrder by ID.
     *
     * @param int $id
     * @return \Digidirect\PreOrder\Api\Data\OrderItemPreorderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id);

    /**
     * Get order item PreOrder by order item ID.
     *
     * @param int $orderItemId
     * @return \Digidirect\PreOrder\Api\Data\OrderItemPreorderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByOrderItemId($orderItemId);

    /**
     * Delete order item PreOrder.
     *
     * @param \Digidirect\PreOrder\Api\Data\OrderItemPreorderInterface $orderItemPreorder
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Digidirect\PreOrder\Api\Data\OrderItemPreorderInterface $orderItemPreorder);

    /**
     * Delete order item PreOrder by ID.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);

    /**
     * Delete order item PreOrder by order item ID.
     *
     * @param int $orderItemId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteByOrderItemId($orderItemId);

    /**
     * Is order item has PreOrder flag
     *
     * @param int $orderItemId
     * @return bool
     */
    public function isOrderItemHasPreorderFlag($orderItemId);
}
