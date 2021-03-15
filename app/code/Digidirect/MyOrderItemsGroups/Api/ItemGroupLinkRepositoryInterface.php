<?php

namespace Digidirect\MyOrderItemsGroups\Api;

/**
 * Interface ItemGroupLinkRepositoryInterface
 * @package Digidirect\MyOrderItemsGroups\Api
 */
interface ItemGroupLinkRepositoryInterface
{
    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param null|int $customerId
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrderItemList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $customerId = null);
}
