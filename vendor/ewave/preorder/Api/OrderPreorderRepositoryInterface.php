<?php

namespace Ewave\PreOrder\Api;

/**
 * OrderPreorder CRUD interface.
 *
 * @api
 */
interface OrderPreorderRepositoryInterface
{
    /**
     * Create or update an order PreOrder.
     *
     * @param \Ewave\PreOrder\Api\Data\OrderPreorderInterface $orderPreorder
     * @return \Ewave\PreOrder\Api\Data\OrderPreorderInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Ewave\PreOrder\Api\Data\OrderPreorderInterface $orderPreorder);

    /**
     * Get order PreOrder by ID.
     *
     * @param int $id
     * @return \Ewave\PreOrder\Api\Data\OrderPreorderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function get($id);

    /**
     * Get order PreOrder by order ID.
     *
     * @param int $orderId
     * @return \Ewave\PreOrder\Api\Data\OrderPreorderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getByOrderId($orderId);

    /**
     * Delete order PreOrder.
     *
     * @param \Ewave\PreOrder\Api\Data\OrderPreorderInterface $orderPreorder
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Ewave\PreOrder\Api\Data\OrderPreorderInterface $orderPreorder);

    /**
     * Delete order PreOrder by ID.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);

    /**
     * Delete order PreOrder by order ID.
     *
     * @param int $orderId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteByOrderId($orderId);

    /**
     * Is order has PreOrder flag
     *
     * @param int $orderId
     * @return bool
     */
    public function isOrderHasPreorderFlag($orderId);
}
