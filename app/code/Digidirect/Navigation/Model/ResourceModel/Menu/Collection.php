<?php

namespace Digidirect\Navigation\Model\ResourceModel\Menu;

use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Digidirect\Navigation\Helper\Data;
use Digidirect\Navigation\Model\Config\Source\Type;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Collection
 *
 * @package Digidirect\Navigation\Model\ResourceModel\Menu
 */
class Collection extends AbstractCollection
{
    const MENU_ITEM_INFO_TABLE = 'digidirect_navigation_menu_item_info';

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Type
     */
    protected $menuItemTypes;

    /**
     * Tables which use "ifnull" condition
     *
     * @var []
     */
    protected $tables;

    /**
     * @var []
     */
    protected $typeCodesArray;

    /**
     * @var []
     */
    protected $tablesData;

    /**
     * @var []
     */
    protected $joinProcessors;

    /**
     * Collection constructor.
     *
     * @param EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param Data $helper
     * @param Type $menuItemTypes
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     * @param array $tablesData
     * @param array $joinProcessors
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Data $helper,
        Type $menuItemTypes,
        AdapterInterface $connection = null,
        AbstractDb $resource = null,
        array $tablesData = [],
        array $joinProcessors = []
    ) {
        $this->helper = $helper;
        $this->menuItemTypes = $menuItemTypes;
        $this->tablesData = $tablesData;
        $this->joinProcessors = $joinProcessors;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Digidirect\Navigation\Model\Menu', 'Digidirect\Navigation\Model\ResourceModel\Menu');
    }

    /**
     * Join attributes table
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinMenuInfoByStore('store_menu_item_info', \Magento\Store\Model\Store::DEFAULT_STORE_ID);
        return $this;
    }

    /**
     * Get fields by table for which we need to use "IFNULL(store_field, default_field)"
     *
     * @param string $tableName
     * @return array|mixed
     */
    protected function _getTablesVariationFields($tableName)
    {
        return $this->tablesData[$tableName]['fields'] ?? [];
    }

    /**
     * Join store info fields
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getStoreId() != \Magento\Store\Model\Store::DEFAULT_STORE_ID) {

            /**
             * Always because get collection and filter only by main table does not make sense
             * and it always joins in _initSelect
             */
            $fields = ['*'];
            $fields = array_merge($fields, $this->_processJoinLeftStore(self::MENU_ITEM_INFO_TABLE));

            if (!empty($this->joinProcessors)) {
                foreach ($this->joinProcessors as $processor) {
                    if (!($processor instanceof JoinTypeInterface)) {
                        continue;
                    }
                    $processor->joinType(
                        $this,
                        'default_',
                        $this->getStoreId()
                    );

                    $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
                    $fields = array_merge(
                        $fields,
                        $this->_ifNull(
                            $processor->getFields(),
                            $processor->getTableAlias(),
                            $processor->getFieldPrefix()
                        )
                    );
                }
            }

            $this->getSelect()->columns($fields);
        }
    }

    /**
     * @return []
     */
    protected function addCustomFields()
    {
        $object = new DataObject(['mst.menu_store_id']);
        $this->_eventManager->dispatch('add_fields_config_before_filter', ['object' => $object]);
        return $object->toArray();
    }

    /**
     * @param array $fields
     * @param string $alias
     * @param string $additionalPrefix
     * @return array
     */
    protected function _ifNull(array $fields, string $alias, $additionalPrefix = '')
    {
        $defaultAlias = 'default_' . $alias;
        $columns = [];
        foreach ($fields as $field) {
            $columns[$additionalPrefix . $field] = $this->getSelect()->getConnection()->getIfNullSql(
                $defaultAlias . '.' . $field,
                $alias . '.' . $field
            );
        }
        return $columns;
    }

    /**
     * Get table alias
     *
     * @param string $table
     * @return string|null
     */
    protected function _getAliasByTable($table)
    {
        return $this->tablesData[$table]['alias'] ?? null;
    }

    /**
     * Perform join by table
     *
     * @param string $table
     * @return []
     */
    protected function _processJoinLeftStore($table)
    {
        $alias = $this->_getAliasByTable($table);
        $defaultAlis = 'default_' . $alias;
        $additionalPrefix = $this->tablesData[$table]['additional_prefix'] ?? '';

        $joinConditions = $this->tablesData[$table]['join_rules'] ?? [];
        $callBack = $joinConditions['callback'] ?? null;

        if (isset($callBack) && is_callable([$this, $callBack])) {
            $this->$callBack($defaultAlis, $this->getStoreId());
        } else {
            $this->_joinTable($defaultAlis, $table, $this->getStoreId(), $this->_getTablesVariationFields($table));
        }

        $columns = $this->_processIfNullCondition($table, $alias, $additionalPrefix);
        $this->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
        return $columns;
    }

    /**
     * @param string $tableAlias
     * @param string $tableName
     * @param int $storeId
     * @param array $fields
     * @return $this
     */
    protected function _joinTable($tableAlias, $tableName, $storeId, array $fields)
    {
        $fieldsToSelect = [];
        foreach ($fields as $field) {
            $fieldsToSelect[] = $tableAlias . '.' . $field;
        }
        $this->getSelect()->joinLeft(
            [$tableAlias => $this->getTable($tableName)],
            'main_table.entity_id = ' . $tableAlias . '.menu_item_id AND ' . $tableAlias . '.store_id =' . $storeId,
            $fieldsToSelect
        );

        return $this;
    }

    /**
     * Add "IFNULL" sql expression
     *
     * @param string $table
     * @param string $storeAlias
     * @param string $fieldPrefix
     * @return []
     */
    protected function _processIfNullCondition($table, $storeAlias, $fieldPrefix = '')
    {
        $fields = $this->_getTablesVariationFields($table);
        return $this->_ifNull($fields, $storeAlias, $fieldPrefix);
    }

    /**
     * Get join condition
     *
     * @param string $tableAlias
     * @param int $storeId
     * @return string
     */
    protected function _getJoinCondition($tableAlias, $storeId)
    {
        return $tableAlias . '.id = main_table.entity_id AND ' . $tableAlias . '.store_id =' . $storeId;
    }

    /**
     * Join Info table by store
     *
     * @param string $tableAlias
     * @param int $store
     * @return $this
     */
    protected function _joinMenuInfoByStore($tableAlias, $store)
    {
        $this->getSelect()->joinLeft(
            [$tableAlias => $this->getTable(self::MENU_ITEM_INFO_TABLE)],
            $this->_getJoinCondition($tableAlias, $store),
            ['*']
        );

        return $this;
    }

    /**
     * Join type link table and all type info tables
     *
     * @return $this
     */
    public function joinTypeInfo()
    {
        $this->getSelect()->joinInner(
            ['type_table' => $this->getTable('digidirect_navigation_menu_item_type')],
            'main_table.type_id = type_table.type_id',
            ['menu_type_code']
        );

        if (!empty($this->joinProcessors)) {
            foreach ($this->joinProcessors as $processor) {
                if (!($processor instanceof JoinTypeInterface)) {
                    continue;
                }
                $processor->joinType(
                    $this,
                    '',
                    $this->helper->getDefaultStoreId()
                );
            }
        }

        $this->getSelect()->group('main_table.entity_id');

        return $this;
    }

    /**
     * Get type code by type id to join specified table
     *
     * @param int $id
     * @return string|null
     */
    public function getTypeById($id)
    {
        if (empty($this->typeCodesArray)) {
            $this->typeCodesArray = $this->menuItemTypes->getTypeIdCodeMapping();
        }
        return $this->typeCodesArray[$id] ?? null;
    }

    /**
     * Get current store ID
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->helper->getCurrentStoreId();
    }

    /**
     * @return []
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('entity_id', 'title');
    }

    /**
     * Set store ID (used for filtering)
     *
     * @param int $storeId
     * @return $this
     */
    public function setCurrentStoreId($storeId)
    {
        $this->helper->setCurrentStoreId($storeId);
        return $this;
    }

    /**
     * Get table prefix for filtering
     *
     * @param string $field
     * @return mixed|null
     */
    protected function _getPrefix($field)
    {
        $queryInfo = explode('.', $field, 2);

        $alias = $queryInfo[0] ?? null;
        $fieldName = $queryInfo[2] ?? null;
        if ($alias && $fieldName) {
            return $field;
        }
        $tablesArray = [];
        foreach ($this->joinProcessors as $joinProcessor) {
            $tablesArray[$joinProcessor->getTable()] = $joinProcessor->getFields();
            if (in_array($field, $tablesArray[$joinProcessor->getTable()])) {
                return $joinProcessor->getTableAlias();
            }
        }

        $allTables = array_keys($this->tablesData);

        foreach ($allTables as $table) {
            $columns = $this->_getTablesVariationFields($table);
            if (in_array($field, $columns)) {
                return $this->_getAliasByTable($table);
            }
        }

        return $field;
    }

    /**
     * Add alias for filtering by store
     *
     * @param array|string $field
     * @param null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $prefix = $this->_getPrefix($field);

        if (!$prefix || $prefix == $field) {
            parent::addFieldToFilter($field, $condition);
            return $this;
        }

        if ($this->getStoreId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID) {
            parent::addFieldToFilter($prefix . '.' . $field, $condition);
            return $this;
        }


        $resource = $this->getResource();
        $connection = $resource->getConnection();

        if (is_array($field)) {
            $conditions = [];
            foreach ($field as $key => $value) {
                $value = $connection->getIfNullSql(
                    'default_' . $prefix . '.' . $value,
                    $prefix . '.' . $value
                );
                $conditions[] = $this->_translateCondition($value, isset($condition[$key]) ? $condition[$key] : null);
            }

            $resultCondition = '(' . implode(') ' . \Magento\Framework\DB\Select::SQL_OR . ' (', $conditions) . ')';
        } else {
            $field = $connection->getIfNullSql(
                'default_' . $prefix . '.' . $field,
                $prefix . '.' . $field
            );
            $resultCondition = $this->_translateCondition($field, $condition);
        }

        $this->getSelect()->where($resultCondition, null, \Magento\Framework\DB\Select::TYPE_CONDITION);
        return $this;
    }

    /**
     * Add stores
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $allIds = $this->getColumnValues('entity_id');
        if (!empty($allIds)) {

            foreach ($this as $item) {
                /**
                 * @var $item \Digidirect\Navigation\Model\Menu
                 */
                $typeCode = $this->getTypeById($item->getTypeId());

                $joinProcessor = $this->joinProcessors[$typeCode] ?? null;
                if ($joinProcessor && $joinProcessor instanceof JoinTypeInterface) {
                    $fieldsToReplace = $joinProcessor->getFields();
                    foreach ($fieldsToReplace as $field) {
                        $item->setData($field, $item->getData($typeCode . '_' . $field));
                    }
                }
                $item->setData('type_code', $typeCode);
            }
        }
        return $this;
    }

    /**
     * Add join expression. Select menu items belong to specified set if this set is enabled and is in current store
     *
     * @param string $setCode
     * @return $this
     */
    public function addSetFilter($setCode)
    {
        $this->getSelect()->joinInner(
            ['set_link_table' => $this->getTable('digidirect_navigation_menu_set_link')],
            'main_table.entity_id = set_link_table.menu_entity_id',
            []
        );

        $codeCondition = 'set_table.set_code = "' . $setCode . '"';

        $this->getSelect()->joinInner(
            ['set_table' => $this->getTable('digidirect_navigation_menu_set')],
            'set_link_table.menu_set_id = set_table.set_id AND set_table.status = 1 AND ' . $codeCondition,
            []
        );

        $this->getSelect()->joinInner(
            ['set_store_table' => $this->getTable('digidirect_navigation_menu_set_store')],
            'set_store_table.menu_set_id = set_table.set_id',
            []
        )->where(
            $this->getConnection()->quoteInto(
                'set_store_table.store_id IN (?)',
                [$this->helper->getDefaultStoreId(), $this->helper->getCurrentStoreId()]
            )
        );

        return $this;
    }

    /**
     * @param null $storeId
     * @return $this
     */
    public function addStoreFilter($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->helper->getCurrentStoreId();
        }
        $this->getSelect()->joinLeft(
            ['mst' => 'digidirect_navigation_menu_item_store_relation'],
            'main_table.entity_id = mst.menu_entity_id',
            ['menu_store_id' => 'mst.menu_store_id']
        )->where(
            $this->getConnection()->quoteInto(
                'mst.menu_store_id IN (?) ',
                [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId]
            )
        );
        return $this;
    }
}
