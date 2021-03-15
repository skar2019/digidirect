<?php

namespace Digidirect\MyOrderItems\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for sales order item search results
 *
 * @api
 * @since 100.0.0
 */
interface StateSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get state list
     *
     * @return \Digidirect\MyOrderItems\Api\Data\OrderItemStateInterface[]
     */
    public function getItems();

    /**
     * Set state list
     *
     * @param \Digidirect\MyOrderItems\Api\Data\OrderItemStateInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
