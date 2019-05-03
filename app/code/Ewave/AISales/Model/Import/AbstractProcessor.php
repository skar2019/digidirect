<?php
namespace Ewave\AISales\Model\Import;

use Ewave\AI\Model\Logger\LoggerInterface;
use Ewave\AISales\Model\Import\AbstractRelationPreparer as RelationPreparer;
use Magento\Framework\App\ResourceConnection;

abstract class AbstractProcessor
{
    const ENTITY_ID = 'entity_id';
    const INCREMENT_ID = 'increment_id';
    const STORE_ID = 'store_id';

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var RelationPreparer[]
     */
    protected $relationPreparers = [];

    /**
     * @var string
     */
    protected $entityTable;

    /**
     * @var string
     */
    protected $entityTableGrid;

    /**
     * @var array
     */
    protected $entityColumns;

    /**
     * @var array
     */
    protected $processedEntities = [];

    /**
     * @param ResourceConnection $resourceConnection
     * @param LoggerInterface $logger
     * @param array $relationPreparers
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        LoggerInterface $logger,
        array $relationPreparers = []
    ) {
        $this->connection = $resourceConnection->getConnection();
        $this->logger = $logger;
        $this->relationPreparers = $relationPreparers;
    }

    /**
     * @param array $entities
     * @param bool $updateOnDuplicate
     * @return bool
     * @throws \Exception
     */
    public function save(array $entities, $updateOnDuplicate = true)
    {
        $this->processedEntities = [];
        try {
            $duplicateEntities = $this->_getExistingEntities($entities);
            $entitiesToAdd = [];
            $entitiesToUpdate = [];
            foreach ($entities as $entity) {
                $entityKey = $entity[self::INCREMENT_ID] . '_' . $entity[self::STORE_ID];
                if (!array_key_exists($entityKey, $duplicateEntities)) {
                    $entitiesToAdd[$entityKey] = $entity;
                } else {
                    $entity[self::ENTITY_ID] = $duplicateEntities[$entityKey];
                    $entitiesToUpdate[$entityKey] = $entity;
                }
            }

            if ($updateOnDuplicate && !empty($entitiesToUpdate)) {
                $this->_updateEntities($entitiesToUpdate);
            }

            if (!empty($entitiesToAdd)) {
                $this->_createEntities($entitiesToAdd);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @param array $entities
     * @return bool
     * @throws \Exception
     */
    public function update(array $entities)
    {
        $this->processedEntities = [];
        try {
            $duplicateEntities = $this->_getExistingEntities($entities);
            $entitiesToUpdate = [];
            foreach ($entities as $entity) {
                $entityKey = $entity[self::INCREMENT_ID] . '_' . $entity[self::STORE_ID];
                if (array_key_exists($entityKey, $duplicateEntities)) {
                    $entity[self::ENTITY_ID] = $duplicateEntities[$entityKey];
                    $entitiesToUpdate[$entityKey] = $entity;
                }
            }

            if (!empty($entitiesToUpdate)) {
                $this->_updateEntities($entitiesToUpdate);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getProcessedEntities()
    {
        return $this->processedEntities;
    }

    /**
     * @param string $incrementId
     * @param int $storeId
     * @return bool
     * @throws \Exception
     */
    public function delete($incrementId, $storeId)
    {
        foreach ([$this->entityTable, $this->entityTableGrid] as $table) {
            $this->connection->delete(
                $this->connection->getTableName($table),
                new \Zend_Db_Expr(sprintf(
                    self::INCREMENT_ID . ' = "%s" AND ' . self::STORE_ID . ' = %d',
                    $incrementId,
                    $storeId
                ))
            );
        }
        return true;
    }

    /**
     * @param array $entities
     * @return void
     */
    protected function _createEntities(array $entities)
    {
        $data = [];
        foreach ($entities as $entity) {
            $data[] = $this->_getEntityRowData($entity);
        }
        $this->connection->insertMultiple(
            $this->connection->getTableName($this->entityTable),
            $data
        );

        $data = [];
        $select = $this->_getFindEntitiesSelect($entities, [self::ENTITY_ID, $this->_getEntityKeyColumn()]);
        $entitiesToAdd = $this->connection->fetchAll($select);

        foreach ($entitiesToAdd as $entityMeta) {
            $entityId = $entityMeta[self::ENTITY_ID];
            $entityData = $entities[$entityMeta['key']];
            $entityData[self::ENTITY_ID] = $entityId;
            $this->processedEntities[$entityId] = $entityData;
            foreach ($this->relationPreparers as $preparer) {
                if ($preparer instanceof RelationPreparer) {
                    $data = array_merge_recursive($data, $preparer->getRow($entityId, $entityData));
                }
            }
        }

        foreach ($data as $table => $insertData) {
            $this->connection->insertMultiple(
                $this->connection->getTableName($table),
                $insertData
            );
        }
    }

    /**
     * @param array $entities
     * @return bool
     * @throws \Exception
     */
    protected function _updateEntities(array $entities)
    {
        $data = [];
        foreach ($entities as $entity) {
            $data[] = $this->_getEntityRowData($entity);
        }
        $this->connection->insertOnDuplicate(
            $this->connection->getTableName($this->entityTable),
            $data
        );

        $data = [];
        foreach ($entities as $entityData) {
            $entityId = $entityData[self::ENTITY_ID];
            $entityData[self::ENTITY_ID] = $entityId;
            $this->processedEntities[$entityId] = $entityData;
            foreach ($this->relationPreparers as $preparer) {
                if ($preparer instanceof RelationPreparer) {
                    $data = array_merge_recursive($data, $preparer->getUpdatedRow($entityId, $entityData));
                }
            }
        }

        foreach ($data as $table => $updatedData) {
            foreach ($updatedData as $updatedRow) {
                $this->connection->insertOnDuplicate(
                    $this->connection->getTableName($table),
                    $updatedRow
                );
            }
        }

        return true;
    }

    /**
     * @param array $entities
     * @return array
     */
    protected function _getExistingEntities(array $entities)
    {
        $select = $this->_getFindEntitiesSelect($entities, [$this->_getEntityKeyColumn(), self::ENTITY_ID]);
        return $this->connection->fetchPairs($select);
    }

    /**
     * @param array $entities
     * @param array $columns
     * @return \Magento\Framework\DB\Select
     */
    protected function _getFindEntitiesSelect(array $entities, array $columns)
    {
        $conditions = [];
        foreach ($entities as $entity) {
            $conditions[] = sprintf(
                self::INCREMENT_ID . ' = "%s" AND ' . self::STORE_ID . ' = %d',
                $entity[self::INCREMENT_ID],
                $entity[self::STORE_ID]
            );
        }

        return $this->connection->select()
            ->from($this->connection->getTableName($this->entityTable), $columns)
            ->where(implode(') OR (', $conditions));
    }

    /**
     * @return \Zend_Db_Expr
     */
    protected function _getEntityKeyColumn()
    {
        return new \Zend_Db_Expr(
            'CONCAT(' . self::INCREMENT_ID . ', "_", ' . self::STORE_ID . ') as `key`'
        );
    }

    /**
     * @param array $entity
     * @return array
     */
    protected function _getEntityRowData(array $entity)
    {
        if ($this->entityColumns === null) {
            $this->entityColumns = $this->connection->describeTable(
                $this->connection->getTableName($this->entityTable)
            );
        }
        return array_intersect_key($entity, $this->entityColumns);
    }
}
