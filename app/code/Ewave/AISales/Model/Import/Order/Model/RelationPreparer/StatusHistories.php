<?php
namespace Ewave\AISales\Model\Import\Order\Model\RelationPreparer;

use Ewave\AISales\Model\Import\AbstractRelationPreparer;
use Ewave\AISales\Model\Import\Order\Model\Processor;
use Magento\Sales\Api\Data\OrderStatusHistoryInterface;

class StatusHistories extends AbstractRelationPreparer
{
    const TABLE = 'sales_order_status_history';

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
     * @return mixed
     */
    public function getRow($orderId, array $orderData)
    {
        return $this->getItemsData(
            $orderData,
            $orderId,
            OrderStatusHistoryInterface::PARENT_ID,
            Processor::COL_STATUS_HISTORIES
        );
    }

    /**
     * @param int $orderId
     * @param array $orderData
     * @return array
     */
    public function getUpdatedRow($orderId, array $orderData)
    {
        return $this->getRow($orderId, $orderData);
    }
}
