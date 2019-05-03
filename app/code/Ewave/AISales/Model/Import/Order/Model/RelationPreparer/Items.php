<?php
namespace Ewave\AISales\Model\Import\Order\Model\RelationPreparer;

use Ewave\AISales\Model\Import\AbstractRelationPreparer;
use Ewave\AISales\Model\Import\Order\Model\Processor;
use Magento\Sales\Api\Data\OrderItemInterface;

class Items extends AbstractRelationPreparer
{
    const TABLE = 'sales_order_item';

    /**
     * @return string
     */
    public function getTable()
    {
        return self::TABLE;
    }

    /**
     * @param int $orderId
     * @param array $orderData
     * @return array
     */
    public function getRow($orderId, array $orderData)
    {
        return $this->getItemsData(
            $orderData,
            $orderId,
            OrderItemInterface::ORDER_ID,
            Processor::COL_ITEMS
        );
    }

    /**
     * @param int $orderId
     * @param array $orderData
     * @return array
     */
    public function getUpdatedRow($orderId, array $orderData)
    {
        return $this->getEntityItemsData(
            $orderData,
            $orderId,
            OrderItemInterface::ORDER_ID,
            OrderItemInterface::ITEM_ID,
            Processor::COL_ITEMS,
            OrderItemInterface::SKU
        );
    }
}
