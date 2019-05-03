<?php
namespace Ewave\AISales\Model\Import\Order\Model\RelationPreparer;

use Ewave\AISales\Model\Import\AbstractRelationPreparer;
use Ewave\AISales\Model\Import\Order\Model\Processor;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class Payments extends AbstractRelationPreparer
{
    const TABLE = 'sales_order_payment';

    /**
     * @var bool
     */
    protected $existPaymentIds;

    /**
     * @var bool
     */
    protected $onlyOnePaymentPerOrder;

    /**
     * Payments constructor.
     *
     * @param ResourceConnection $resourceConnection
     * @param bool $onlyOnePaymentPerOrder
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        $onlyOnePaymentPerOrder = true
    ) {
        parent::__construct($resourceConnection);
        $this->onlyOnePaymentPerOrder = $onlyOnePaymentPerOrder;
    }

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
        $data = $this->getItemsData(
            $orderData,
            $orderId,
            OrderPaymentInterface::PARENT_ID,
            Processor::COL_PAYMENTS
        );

        if ($this->onlyOnePaymentPerOrder and $this->getExistPaymentId($orderId)) {
            foreach ($data[self::TABLE] as &$payment) {
                $payment[OrderPaymentInterface::ENTITY_ID] = $this->getExistPaymentId($orderId);
            }
        }

        return $data;
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

    /**
     * @param integer $orderId
     * @return string
     */
    protected function getExistPaymentId($orderId)
    {
        if (!empty($this->existPaymentIds[$orderId])) {
            return $this->existPaymentIds[$orderId];
        }
        $select = $this->connection->select()
            ->from(['sop' => $this->connection->getTableName(self::TABLE)], 'sop.' . OrderPaymentInterface::ENTITY_ID)
            ->where('sop.' . OrderPaymentInterface::PARENT_ID . ' = ?', $orderId)
            ->limit(1);
        $this->existPaymentIds[$orderId] = $this->connection->fetchOne($select);

        return $this->existPaymentIds[$orderId];
    }
}
