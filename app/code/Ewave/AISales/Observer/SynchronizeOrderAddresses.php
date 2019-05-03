<?php
namespace Ewave\AISales\Observer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Helper\Address;

class SynchronizeOrderAddresses implements ObserverInterface
{
    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @param ResourceConnection $resourceConnection
     * @internal param Grid $grid
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->connection = $resourceConnection->getConnection();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        $entities = $observer->getEvent()->getData('entities');
        if (empty($entities)) {
            return;
        }
        $orderIds = array_column($entities, \Ewave\AISales\Model\Import\AbstractProcessor::ENTITY_ID);
        if (empty($orderIds)) {
            return;
        }
        $orderIds = array_unique($orderIds);
        $this->updateAddressIdsWithOrders($orderIds);
    }

    /**
     * @param array $orderIds
     * @return $this
     */
    protected function updateAddressIdsWithOrders(array $orderIds)
    {
        $salesOrderTbl = $this->connection->getTableName('sales_order');
        $salesOrderAddressTbl = $this->connection->getTableName('sales_order_address');
        $billing = Address::TYPE_BILLING;
        $shipping = Address::TYPE_SHIPPING;

        $where = $this->connection->quoteInto('so.entity_id IN (?)', $orderIds);
        $query = "
        UPDATE $salesOrderTbl so
          LEFT JOIN $salesOrderAddressTbl billing_address
            ON billing_address.parent_id = so.entity_id AND billing_address.address_type = '$billing'
          LEFT JOIN $salesOrderAddressTbl shipping_address
            ON shipping_address.parent_id = so.entity_id AND shipping_address.address_type = '$shipping'
        SET so.billing_address_id = billing_address.entity_id, so.shipping_address_id = shipping_address.entity_id
        WHERE {$where}
        ";
        $this->connection->query($query);

        return $this;
    }
}
