<?php
namespace Ewave\AISales\Model\Import;

use Magento\Framework\App\ResourceConnection;

abstract class AbstractRelationPreparer
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var array
     */
    protected $columns;

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
     * @return string
     */
    abstract public function getTable();

    /**
     * @param int $entityId
     * @param array $entityData
     * @return array
     */
    abstract public function getRow($entityId, array $entityData);

    /**
     * @param int $entityId
     * @param array $entityData
     * @return array
     */
    abstract public function getUpdatedRow($entityId, array $entityData);

    /**
     * @return array
     */
    public function getColumns()
    {
        if ($this->columns === null) {
            $this->columns = $this->connection->describeTable($this->getTable());
        }
        return $this->columns;
    }

    /**
     * @param array $entity
     * @param int $entityId
     * @param string $colItems
     * @param string $entityIdKey
     * @return array
     */
    protected function getItemsData(array $entity, $entityId, $entityIdKey, $colItems)
    {
        $data = [];
        if (!isset($entity[$colItems])) {
            return $data;
        }
        foreach ($entity[$colItems] as $item) {
            $item[$entityIdKey] = $entityId;
            $data[$this->getTable()][] = array_intersect_key($item, $this->getColumns());
        }
        return $data;
    }

    /**
     * @param array $entity
     * @param int $entityId
     * @param string $entityIdKey
     * @param string $itemIdKey
     * @param string $colItems
     * @param string $identityKey
     * @return array
     */
    protected function getEntityItemsData(array &$entity, $entityId, $entityIdKey, $itemIdKey, $colItems, $identityKey)
    {
        $data = [];
        if (!isset($entity[$colItems])) {
            return $data;
        }
        foreach ($entity[$colItems] as &$item) {
            $item[$entityIdKey] = $entityId;
            if (isset($item[$itemIdKey])) {
                $data[$this->getTable()][] = array_intersect_key($item, $this->getColumns());
            } elseif (isset($item[$identityKey])) {
                if ($itemId = $this->_getItemIdBySku(
                    $entityId,
                    $item[$identityKey],
                    $entityIdKey,
                    $identityKey,
                    $itemIdKey
                )) {
                    $item[$itemIdKey] = $itemId;
                }
                $data[$this->getTable()][] = array_intersect_key($item, $this->getColumns());
            }
        }
        return $data;
    }

    /**
     * @param int $entityId
     * @param string $identityValue
     * @param string $entityIdKey
     * @param string $identityKey
     * @param string $itemIdKey
     * @return int
     */
    protected function _getItemIdBySku($entityId, $identityValue, $entityIdKey, $identityKey, $itemIdKey)
    {
        $select = $this->connection->select()
            ->from($this->connection->getTableName($this->getTable()), [$itemIdKey])
            ->where(new \Zend_Db_Expr($entityIdKey . ' = :parentId AND ' . $identityKey . ' = :identityValue'));

        return (int)$this->connection->fetchOne($select, [
            'parentId' => $entityId,
            'identityValue' => $identityValue
        ]);
    }
}
