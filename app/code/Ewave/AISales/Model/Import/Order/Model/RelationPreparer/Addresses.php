<?php
namespace Ewave\AISales\Model\Import\Order\Model\RelationPreparer;

use Ewave\AISales\Model\Import\AbstractRelationPreparer;
use Ewave\AISales\Model\Import\Order\Model\Processor;
use Magento\Sales\Api\Data\OrderAddressInterface;

class Addresses extends AbstractRelationPreparer
{
    const TABLE = 'sales_order_address';
    const TYPE_BILLING = 'billing';
    const TYPE_SHIPPING = 'shipping';

    /**
     * @var array
     */
    protected $addressTypes = [
        self::TYPE_BILLING => Processor::COL_BILLING_ADDRESS,
        self::TYPE_SHIPPING => Processor::COL_SHIPPING_ADDRESS,
    ];

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
        $data = [];
        foreach ($this->addressTypes as $type => $column) {
            if (isset($orderData[$column]) && !empty($orderData[$column])) {
                $orderData[$column][OrderAddressInterface::PARENT_ID] = $orderId;
                $orderData[$column][OrderAddressInterface::ADDRESS_TYPE] = $type;
                $data[$this->getTable()][] = array_intersect_key($orderData[$column], $this->getColumns());
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
        $addressesToDelete = [];
        foreach ($this->addressTypes as $type => $column) {
            if (isset($orderData[$column]) && !isset($orderData[$column][OrderAddressInterface::ENTITY_ID])) {
                $addressesToDelete[] = $type;
            }
        }

        $this->connection->delete($this->getTable(), sprintf(
            'parent_id = %d AND address_type IN ("%s")',
            $orderId,
            implode('", "', $addressesToDelete)
        ));

        return $this->getRow($orderId, $orderData);
    }
}
