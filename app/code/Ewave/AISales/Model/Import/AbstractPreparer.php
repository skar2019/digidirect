<?php
namespace Ewave\AISales\Model\Import;

use Ewave\AI\Model\Lib\Entity\Import\Service\PreparerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;

abstract class AbstractPreparer implements PreparerInterface
{
    const ORDER_TABLE           = 'sales_order';
    const ORDER_ITEM_TABLE      = 'sales_order_item';
    const PRODUCT_TABLE         = 'catalog_product_entity';
    const PRODUCT_ENTITY_ID     = 'entity_id';

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * RelationPreparerAbstract constructor.
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->connection = $resourceConnection->getConnection();
    }

    /**
     * @param array $entity
     * @param string $orderIdKey
     * @param string $incrementIdKey
     * @return array
     */
    protected function setEntityOrderId($entity, $orderIdKey, $incrementIdKey)
    {
        if (!isset($entity[$orderIdKey]) && isset($entity[$incrementIdKey])) {
            $entity[$orderIdKey] = $this->getOrderId(
                $entity[$incrementIdKey],
                $entity[OrderInterface::STORE_ID]
            );
        }
        return $entity;
    }

    /**
     * @param array $entity
     * @param string $itemIdKey
     * @param string $orderItemIdKey
     * @param string $orderIdKey
     * @param string $colItems
     * @return array
     */
    protected function setItemsIds($entity, $itemIdKey, $orderItemIdKey, $orderIdKey, $colItems)
    {
        if (!isset($entity[$colItems])) {
            return $entity;
        }
        foreach ($entity[$colItems] as &$item) {
            if (isset($item[$itemIdKey])) {
                continue;
            }
            if (!isset($item[$orderItemIdKey]) && isset($entity[$orderIdKey])) {
                $item[$orderItemIdKey] = $this->getOrderItemId(
                    $entity[$orderIdKey],
                    $item[ProductInterface::SKU]
                );
            }
        }
        return $entity;
    }

    /**
     * @param array $entity
     * @param string $itemIdKey
     * @param string $colItems
     * @return array
     */
    protected function setItemsProductData($entity, $itemIdKey, $colItems)
    {
        if (!isset($entity[$colItems])) {
            return $entity;
        }
        foreach ($entity[$colItems] as &$item) {
            if (isset($item[$itemIdKey])) {
                continue;
            }

            if (!isset($item[OrderInterface::STORE_ID]) || isset($entity[OrderInterface::STORE_ID])) {
                $item[OrderInterface::STORE_ID] = $entity[OrderInterface::STORE_ID];
            }

            if (!isset($item[OrderItemInterface::PRODUCT_ID])) {
                $productData = $this->getProductDataBySku(
                    $item[OrderItemInterface::SKU],
                    [self::PRODUCT_ENTITY_ID, ProductInterface::TYPE_ID]
                );

                if (!empty($productData)) {
                    $item[OrderItemInterface::PRODUCT_ID] = $productData[self::PRODUCT_ENTITY_ID];
                    $item[OrderItemInterface::PRODUCT_TYPE] = $productData[ProductInterface::TYPE_ID];
                }
            }
        }
        return $entity;
    }

    /**
     * @param string $incrementId
     * @param int $storeId
     * @return array
     */
    protected function getOrderId($incrementId, $storeId)
    {
        $select = $this->connection->select()
            ->from($this->connection->getTableName(self::ORDER_TABLE), [OrderInterface::ENTITY_ID])
            ->where(OrderInterface::INCREMENT_ID . ' = :incrementId AND ' . OrderInterface::STORE_ID . ' = :storeId');

        return $this->connection->fetchOne($select, [
            'incrementId' => $incrementId,
            'storeId' => $storeId,
        ]);
    }

    /**
     * @param int $orderId
     * @param string $sku
     * @return array
     */
    protected function getOrderItemId($orderId, $sku)
    {
        $select = $this->connection->select()
            ->from($this->connection->getTableName(self::ORDER_ITEM_TABLE), [OrderItemInterface::ITEM_ID])
            ->where(OrderItemInterface::ORDER_ID . ' = :orderId AND ' . OrderItemInterface::SKU . ' = :sku');

        return $this->connection->fetchOne($select, [
            'orderId' => $orderId,
            'sku' => $sku
        ]);
    }

    /**
     * @param string $sku
     * @param array $columns
     * @return array
     */
    protected function getProductDataBySku($sku, array $columns = [])
    {
        $select = $this->connection->select()
            ->from($this->connection->getTableName(self::PRODUCT_TABLE), $columns)
            ->where(ProductInterface::SKU . ' = ?', $sku);

        return $this->connection->fetchRow($select);
    }
}
