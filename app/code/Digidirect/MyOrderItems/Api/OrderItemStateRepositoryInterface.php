<?php

namespace Digidirect\MyOrderItems\Api;

/**
 * @api
 * @since 100.0.0
 */
interface OrderItemStateRepositoryInterface
{
    /**
     * @param int $id
     * @return Data\OrderItemStateInterface
     */
    public function getById($id);

    /**
     * @param Data\OrderItemStateInterface $orderItemState
     * @param null $customerId
     * @return mixed
     */
    public function save(\Digidirect\MyOrderItems\Api\Data\OrderItemStateInterface $orderItemState, $customerId = null);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param int $customerId
     * @return \Magento\Sales\Api\Data\OrderItemSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrderItemList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $customerId = null);
}
