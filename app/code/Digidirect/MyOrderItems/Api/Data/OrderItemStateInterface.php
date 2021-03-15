<?php

namespace Digidirect\MyOrderItems\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface MyOrderItems
 * @package Digidirect\MyOrderItems\Api\Data
 */
interface OrderItemStateInterface extends ExtensibleDataInterface
{
    /**
     * Sales item id
     */
    const SALES_ITEM_ID = 'sales_item_id';

    /**
     * Sales order item status
     */
    const STATUS = 'status';

    /**
     * Sales order item sort_order
     */
    const SORT_ORDER  = 'sort_order';

    /**
     * Sales order item date_of_purchase
     */
    const DATE_OF_PURCHASE = 'date_of_purchase';

    /**
     * Status enabled
     */
    const ENABLED = 1;

    /**
     * Status disabled
     */
    const DISABLED = 0;

    /**
     * Get Sales Item Id
     *
     * @return int
     */
    public function getSalesItemId();

    /**
     * Get Status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Get Sort Order
     *
     * @return int
     */
    public function getSortOrder();

    /**
     * Set Sales Item Id
     *
     * @param int $salesItemId
     * @return \Digidirect\MyOrderItems\Api\Data\OrderItemStateInterface
     */
    public function setSalesItemId($salesItemId);

    /**
     * Set Status
     *
     * @param int $status
     * @return \Digidirect\MyOrderItems\Api\Data\OrderItemStateInterface
     */
    public function setStatus($status);

    /**
     * Set Sort Order
     *
     * @param string $sortOrder
     * @return \Digidirect\MyOrderItems\Api\Data\OrderItemStateInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Digidirect\MyOrderItems\Api\Data\OrderItemStateExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Digidirect\MyOrderItems\Api\Data\OrderItemStateExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Digidirect\MyOrderItems\Api\Data\OrderItemStateExtensionInterface $extensionAttributes
    );
}
