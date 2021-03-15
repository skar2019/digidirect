<?php

namespace Digidirect\MyOrderItemsGroups\Api;

/**
 * Interface OrderItemGroupRepositoryInterface
 * @package Digidirect\MyOrderItemsGroups\Api
 */
interface OrderItemGroupRepositoryInterface
{
    /**
     * @param int $id
     * @return Data\OrderItemGroupInterface
     */
    public function getById($id);

    /**
     * @param Data\OrderItemGroupInterface $orderItemGroup
     * @return mixed|void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(Data\OrderItemGroupInterface $orderItemGroup);

    /**
     * @param Data\OrderItemGroupInterface $orderItemGroup
     * @return mixed|void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(Data\OrderItemGroupInterface $orderItemGroup);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupSearchResultInterface
     */
    public function getOrderItemGroupList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
