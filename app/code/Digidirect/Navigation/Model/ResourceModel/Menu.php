<?php

namespace Digidirect\Navigation\Model\ResourceModel;

use Digidirect\Navigation\Model\NotFilteredTypes;
use Digidirect\Navigation\Model\TypeIdMapper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;

/**
 * Class Menu
 *
 * @package Digidirect\Navigation\Model\ResourceModel
 */
class Menu extends AbstractDb
{
    const DIGIDIRECT_NAVIGATION_MENU_ITEM_INFO_TABLE = 'digidirect_navigation_menu_item_info';
    const PHONE_PREFIX = 'tel:';

    /**
     * @var \Digidirect\Navigation\Helper\Data
     */
    protected $helper;

    /**
     * @var NotFilteredTypes
     */
    protected $typesCollectionFactory;

    /**
     * @var array
     */
    protected $typesCodesMapping = [];

    /**
     * @var null
     */
    protected $typeId = null;

    /**
     * @var \Magento\Framework\Db\Select
     */
    protected $select;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var Type\ProcessorFactory
     */
    protected $processorFactory;

    /**
     * Menu constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Digidirect\Navigation\Helper\Data $helper
     * @param NotFilteredTypes $collectionFactory
     * @param Type\ProcessorFactory $processorFactory
     * @param null $connectionName
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Digidirect\Navigation\Helper\Data $helper,
        NotFilteredTypes $collectionFactory,
        \Digidirect\Navigation\Model\ResourceModel\Type\ProcessorFactory $processorFactory,
        $connectionName = null,
        array $data = []
    ) {
        parent::__construct($context, $connectionName);
        $this->helper = $helper;
        $this->typesCollectionFactory = $collectionFactory;
        $this->data = $data;
        $this->processorFactory = $processorFactory;
    }

    /**
     * Set main table name anf it's primary key field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('digidirect_navigation_menu_entity', 'entity_id');
    }

    /**
     * Modify load select - add default data if current store is not admin default
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $this->select = $select;
        $select = clone $select;

        $result = $this->getConnection()->fetchRow($select);
        $typeId = isset($result['type_id']) ? $result['type_id'] : null;
        if (!$typeId) {
            return $select;
        }

        return $this->_retrieveFullMenuInfo($select, $this->helper->getCurrentStoreId());
    }

    /**
     * Get menu set info by join table
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    protected function _joinSetInfo(\Magento\Framework\DB\Select $select)
    {
        $select->joinLeft(
            ['menu_set' => $this->getTable('digidirect_navigation_menu_set')],
            'menu_item_set_link.menu_set_id = menu_set.set_id',
            ['set_id' => new \Zend_Db_Expr("GROUP_CONCAT(menu_set.set_id SEPARATOR ',')")]
        );

        return $select;
    }

    /**
     * Join all required tables by store ID
     *
     * @param \Magento\Framework\DB\Select $select
     * @param int $storeId
     * @return \Magento\Framework\DB\Select
     */
    protected function _retrieveFullMenuInfo(\Magento\Framework\DB\Select $select, $storeId)
    {
        $mainTable = $this->getMainTable();

        $result = $this->getConnection()->fetchRow($select);
        $typeId = $result['type_id'] ?? null;
        if (!$typeId) {
            return $select;
        }

        $typeCode = $this->getTypeCodeById($typeId);

        $select->joinInner(
            ['navigation_menu_type_table' => $this->getTable('digidirect_navigation_menu_item_type')],
            $mainTable . '.type_id = navigation_menu_type_table.type_id',
            ['*']
        );

        $className = $this->data['save_processors'][$typeCode];
        $instance = $this->processorFactory->get($className);
        $instance->getJoinRule($select, $this->getMainTable(), $storeId);

        $select->joinLeft(
            ['menu_information_table' => $this->_getMainInfoTable()],
            $mainTable . '.entity_id = menu_information_table.id AND menu_information_table.store_id = ' . $storeId,
            ['item_info' => '*']
        );

        $select->joinLeft(
            ['menu_item_set_link' => $this->getTable('digidirect_navigation_menu_set_link')],
            $mainTable . '.entity_id = menu_item_set_link.menu_entity_id',
            ['menu_item_set_link' => '*']
        );

        $select = $this->_joinSetInfo($select);
        return $select;
    }

    /**
     * Get table to join by menu item type code
     *
     * @param string $typeCode
     * @return string
     */
    public function getLinkInformationTableByCode($typeCode)
    {
        return $this->getTable('digidirect_navigation_menu_' . $typeCode . '_type');
    }

    /**
     * Add data from default store
     *
     * @param \Magento\Framework\Model\AbstractModel $menu
     * @return $this
     */
    protected function _afterLoad(AbstractModel $menu)
    {
        /**
         * @var $menu \Digidirect\Navigation\Model\Menu
         */
        $menuId = $menu->getId();
        if ($menuId) {
            if ($this->helper->getCurrentStoreId() != $this->helper->getDefaultStoreId()) {
                $select = $this->_retrieveFullMenuInfo($this->select, $this->helper->getDefaultStoreId());
                $defaultData = $this->getConnection()->fetchRow($select);
                $menu->addDataDefault($defaultData);
            }
            $menu->setMenuStoreId($this->getMenuStoreIds($menu));
        }

        parent::_afterLoad($menu);
        return $this;
    }

    /**
     * Update related tables after save
     *
     * @param \Magento\Framework\Model\AbstractModel $menu
     * @return $this
     */
    protected function _afterSave(AbstractModel $menu)
    {
        parent::_afterSave($menu);
        if ($menu->getId()) {
            $this->_updateMenuItemInfo($menu);
            $this->_callSaveProcessor($menu);
            $this->_updateMenuItemSetLinkTable($menu);
            $this->saveMenuItemStoreRelation($menu);
        }

        return $this;
    }

    /**
     * Map data before save
     *
     * @since 1.3.0
     * @param AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $typeId = $object->getTypeId();
        /**
         * @var $typeIdMapper TypeIdMapper
         */
        $typeIdMapper = ObjectManager::getInstance()->get(TypeIdMapper::class);
        $typeId = $typeIdMapper->getDbIdByExpectedId($typeId);
        $object->setTypeId($typeId);
        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $menu
     * @return AbstractModel
     */
    protected function _callSaveProcessor(AbstractModel $menu)
    {
        $typeCode = $this->getTypeCodeById($menu->getTypeId());
        $className = $this->data['save_processors'][$typeCode] ?? null;
        if (!$className) {
            return $menu;
        }

        $instance = $this->processorFactory->get($className);
        $instance->saveInformation($menu);

        return $menu;
    }

    /**
     * Update main info table
     *
     * @param \Magento\Framework\Model\AbstractModel $menu
     * @return \Magento\Framework\Model\AbstractModel
     */
    protected function _updateMenuItemInfo(AbstractModel $menu)
    {
        $storeIds = $menu->getStores();
        $menuItemInfoData = [];
        $useDefaultData = $menu->getUseDefault();
        $canUseDefault = !empty($useDefaultData);
        if (!empty($useDefaultData)) {
            foreach ($useDefaultData as $field => $value) {
                $menuItemInfoData[$field] = $value ? null : $menu->getData($field);
            }
        }

        $parentMenuItemId = ($canUseDefault ? $menuItemInfoData['parent_menu_item_id'] ?? null
            : $menu->getData('parent_menu_item_id') ?? 0);

        $pathInfo = $this->_makePath($menu, (int)$parentMenuItemId);
        $path = $pathInfo['path'];
        $level = $pathInfo['level'];
        $menu->setNewPath($path);
        $menu->setNewLevel($level);

        if ($canUseDefault && null === $parentMenuItemId) {
            $menu->setNewPath(null);
            $menu->setNewLevel(null);
            $path = null;
            $level = null;
        }

        $fields = $this->_getInfoTableFields();

        $toSave = [];
        foreach ($storeIds as $storeId) {
            foreach ($fields as $field) {
                $toSave[$storeId][$field] = $canUseDefault ? $menuItemInfoData[$field] ?? null : $menu->getData($field);
            }
            $toSave[$storeId]['id'] = $menu->getId();
            $toSave[$storeId]['path'] = $path;
            $toSave[$storeId]['store_id'] = $storeId;
            $toSave[$storeId]['level'] = $level;
            $toSave[$storeId]['parent_menu_item_id'] = $parentMenuItemId;
        }

        $this->getConnection()->insertOnDuplicate(
            $this->_getMainInfoTable(),
            $toSave
        );

        $this->_rebuildTree($menu, $storeIds);

        return $menu;
    }

    /**
     * Get menu item info table fields
     *
     * @return array
     */
    protected function _getInfoTableFields()
    {
        return $this->data['tables_info']['digidirect_navigation_menu_item_info']['fields']['to_save'] ?? [];
    }

    /**
     * Rebuild menu tree
     * if saved menu item parent changed - replace all its children paths
     * if not changed - do nothing
     *
     * @param AbstractModel $menu
     * @param [] $storeIds
     * @return $this
     */
    protected function _rebuildTree(AbstractModel $menu, $storeIds = [0])
    {
        $oldPath = $menu->getPath();
        $oldLevel = $menu->getLevel();
        $newPath = $menu->getNewPath();

        if ($oldPath && $oldLevel) {
            if ($oldPath != $newPath) {
                foreach ($storeIds as $storeId) {
                    $this->_replace($oldPath, $newPath, $storeId);
                }
                return $this;
            } else {
                return $this;
            }
        }

        return $this;
    }

    /**
     * Make path ann level
     *
     * @param AbstractModel $menu
     * @param int $parentMenuId
     * @return []
     */
    protected function _makePath(AbstractModel $menu, $parentMenuId = 0)
    {
        if (0 == $parentMenuId || null === $parentMenuId) {
            $path = 0 . '/' . $menu->getId();
            $level = count(explode('/', $path)) - 1;
            return ['path' => $path, 'level' => $level];
        }
        $parentMenuInfo = $this->_getParentMenuInfo($parentMenuId);
        $path = ($parentMenuInfo['path'] ?? 0) . '/' . $menu->getId();
        $level = count(explode('/', $path)) - 1;

        return ['level' => $level, 'path' => $path];
    }

    /**
     * Get parent menu item path and level info
     *
     * @param int $id
     * @return []
     */
    protected function _getParentMenuInfo($id)
    {
        if ($id > 0) {
            $select = $this->getConnection()->select()
                ->from($this->_getMainInfoTable(), ['path', 'level'])
                ->where($this->getConnection()->quoteInto('id = ?', $id));
            return $this->getConnection()->fetchRow($select);
        }

        return [];
    }

    /**
     * Update main info type table
     *
     * @param \Magento\Framework\Model\AbstractModel $menu
     * @return \Magento\Framework\Model\AbstractModel
     */
    protected function _updateMenuItemInfoType(AbstractModel $menu)
    {
        $storeIds = $menu->getStores();
        $useDefaultData = $menu->getUseDefault();
        $canUseDefault = !empty($useDefaultData);

        $info = [];
        $typeCode = $this->getTypeCodeById($menu->getTypeId());
        foreach ($storeIds as $storeId) {
            foreach ($this->_getTableFieldsByType($typeCode) as $fieldName) {
                $info[$storeId][$fieldName] = $canUseDefault ?
                    ($useDefaultData[$fieldName] ? null : $menu->getData($fieldName))
                    : $menu->getData($fieldName);
            }
            $info[$storeId]['store_id'] = $storeId;
            $info[$storeId]['menu_item_id'] = $menu->getId();
        }

        $this->getConnection()->insertOnDuplicate(
            $this->getLinkInformationTableByCode($typeCode),
            $info
        );

        return $menu;
    }

    /**
     * Get table columns to update
     *
     * @param string $typeCode
     * @return []
     */
    protected function _getTableFieldsByType($typeCode)
    {
        return $this->data['tables_info'][$this->getLinkInformationTableByCode($typeCode)]['fields']['to_save'] ?? [];
    }

    /**
     * Update relationship between menu and menu set
     *
     * @param AbstractModel $menu
     * @return AbstractModel
     */
    protected function _updateMenuItemSetLinkTable(AbstractModel $menu)
    {
        $setIds = $menu->getSetId() ?? [];
        $oldIdsSelect = $this->getConnection()->select()
            ->from('digidirect_navigation_menu_set_link', ['menu_set_id'])
            ->where($this->getConnection()->quoteInto('menu_entity_id =?', $menu->getId()));

        $oldIds = $this->getConnection()->fetchCol($oldIdsSelect) ?? [];

        $setsToInsert = array_diff($setIds, $oldIds);
        $setsToRemove = array_diff($oldIds, $setIds);

        /**
         * Insert new sets ids
         */
        if (!empty($setsToInsert)) {
            $data = [];
            foreach ($setsToInsert as $key => $set) {
                $data[$key]['menu_set_id'] = $set;
                $data[$key]['menu_entity_id'] = $menu->getId();
            }

            $this->getConnection()->insertOnDuplicate(
                $this->getTable('digidirect_navigation_menu_set_link'),
                $data
            );
        }

        /**
         * Remove relation do not need
         */
        if ($setsToRemove) {
            $this->getConnection()->delete(
                $this->getTable('digidirect_navigation_menu_set_link'),
                [
                    $this->getConnection()->quoteInto('menu_set_id IN (?)', $setsToRemove),
                    $this->getConnection()->quoteInto('menu_entity_id = ?', $menu->getId()),
                ]
            );
        }

        return $menu;
    }

    /**
     * Get type code by id (use to join table)
     *
     * @param int $id
     * @return string|null
     */
    public function getTypeCodeById($id)
    {
        if (empty($this->typesCodesMapping)) {
            $typesCollection = $this->typesCollectionFactory->getCollection();
            foreach ($typesCollection as $type) {
                $this->typesCodesMapping[$type->getId()] = $type->getMenuTypeCode();
            }
        }

        return $this->typesCodesMapping[$id] ?? null;
    }

    /**
     * Rebuild tree
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _afterDelete(AbstractModel $object)
    {
        parent::_afterDelete($object);
        $path = $object->getPath();
        $idsToDelete = $this->_getChildrenMenuItemsIds($path);
        $this->getConnection()->delete(
            $this->getMainTable(),
            $this->getConnection()->quoteInto('entity_id IN (?)', $idsToDelete)
        );
        return $this;
    }

    /**
     * Replace all paths
     *
     * @param string $oldPath
     * @param string $newPath
     * @param int $storeId
     * @return $this
     */
    protected function _replace($oldPath, $newPath, $storeId = null)
    {
        $table = $this->_getMainInfoTable();
        $connection = $this->getConnection();
        $pathField = $connection->quoteIdentifier('path');
        $whereConditions = [
            $pathField . ' LIKE ?' => $oldPath . '/%',
        ];

        if ($storeId) {
            $whereConditions += ['store_id = ?' => $storeId];
        }

        /**
         * Update children nodes path
         */
        if (null === $newPath) {
            $connection->update(
                $table,
                [
                    'path' => null,
                    'level' => null,
                    'parent_menu_item_id' => null,
                ],
                $whereConditions
            );
            return $this;
        }

        $levelFiled = $connection->quoteIdentifier('level');
        $oldLevel = count(explode('/', $oldPath)) - 1;
        $newLevel = count(explode('/', $newPath)) - 1;

        $levelDisposition = $newLevel - $oldLevel;

        $connection->update(
            $table,
            [
                'path' => new \Zend_Db_Expr(
                    'REPLACE(' . $pathField . ',' . $connection->quote(
                        $oldPath . '/'
                    ) . ', ' . $connection->quote(
                        $newPath . '/'
                    ) . ')'
                ),
                'level' => new \Zend_Db_Expr($levelFiled . ' + ' . $levelDisposition),
            ],
            $whereConditions
        );

        return $this;
    }

    /**
     * Delete record from menu item type info table
     *
     * @param string $table
     * @param [] $entityIds
     * @param int $storeId
     * @return int
     */
    protected function _deleteRecordFromTypeInfoTable($table, $entityIds, $storeId = 0)
    {
        return $this->getConnection()->delete($table, [
            'menu_item_id IN (?)' => $entityIds,
            'store_id = ?' => $storeId,
        ]);
    }

    /**
     * Get all child items ids
     *
     * @param int $parentItemId
     * @param int $storeId
     * @return string
     */
    protected function _getItemPathByStoreAndId($parentItemId, $storeId = 0)
    {
        $infoTable = $this->_getMainInfoTable();
        $connection = $this->getConnection();

        $menuInfoSelect = $connection->select()
            ->from(['info_table' => $infoTable], ['path'])
            ->where($connection->quoteInto('id = ?', $parentItemId))
            ->where($connection->quoteInto('store_id = ?', $storeId));
        return $connection->fetchOne($menuInfoSelect);
    }

    /**
     * Delete menu info by store ID and menu entity ID
     *
     * @param int $entityId
     * @param int $storeId
     * @return $this
     */
    public function deleteByIdAndStoreId($entityId, $storeId)
    {
        $connection = $this->getConnection();

        $path = $this->_getItemPathByStoreAndId($entityId, $storeId);
        $allIds = $this->_getChildrenMenuItemsIds($path, $storeId);
        array_unshift($allIds, $entityId);

        $this->_deleteFromInfoTable($allIds, $storeId);

        $typeCode = $connection->select()
            ->from(
                ['main_table' => $this->getMainTable()],
                []
            )->joinInner(
                ['type_table' => $this->getTable('digidirect_navigation_menu_item_type')],
                'main_table.type_id = type_table.type_id',
                ['type_code' => 'menu_type_code']
            )->where($connection->quoteInto('main_table.entity_id IN (?)', $allIds));

        $codes = $connection->fetchCol($typeCode, 'type_code');
        foreach (array_unique($codes) as $code) {
            $table = $this->getLinkInformationTableByCode($code);
            $this->_deleteRecordFromTypeInfoTable($table, $allIds, $storeId);
        }

        return $this;
    }

    /**
     * Delete records
     *
     * @param array $allIds
     * @param int $storeId
     * @return void
     */
    protected function _deleteFromInfoTable(array $allIds, $storeId = 0)
    {
        $this->getConnection()->delete(
            $this->_getMainInfoTable(),
            [
                'id IN (?)' => $allIds,
                'store_id = ?' => $storeId,
            ]
        );
    }

    /**
     * Update status by ids
     *
     * @param [] $id
     * @param int $status
     * @return int
     */
    public function updateStatus($ids, $status)
    {
        $connection = $this->getConnection();
        return $connection->update(
            $this->_getMainInfoTable(),
            [
                'status' => $status,
            ],
            $connection->quoteInto('id IN (?)', $ids)
        );
    }

    /**
     * Get main info table
     *
     * @return string
     */
    protected function _getMainInfoTable()
    {
        return $this->getTable(self::DIGIDIRECT_NAVIGATION_MENU_ITEM_INFO_TABLE);
    }

    /**
     * Validate: first of all check if specified parent_item_id is used as child of current item
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @param int $storeId
     * @param int $menuId
     * @return []
     */
    public function validate($request, $storeId, $menuId)
    {
        $errors = [];
        $parentId = $request->getParam('parent_menu_item_id', null);
        $entityId = $request->getParam('entity_id');
        $code = $request->getParam('menu_item_code');
        $link = $request->getParam('link');

        if ($parentId && $menuId) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from($this->_getMainInfoTable(), ['path'])
                ->where($connection->quoteInto('id = ?', $parentId))
                ->where($connection->quoteInto('store_id = ?', $storeId));

            $path = $connection->fetchOne($select);
            $path = $path ? explode('/', $path) : [];

            in_array($menuId, $path)
                ? $errors[] = __('Specified parent menu item is already used as child of current item') : null;
        }
        if ($this->getMenuItemByCode($code, $entityId)) {
            $errors[] = __('Menu item code must be unique');
        }
        if ($link && $this->helper->hasPhonePrefix($link)
            && !is_numeric(substr($this->helper->removeSpaces($link), 4))
        ) {
            $errors[] = __(
                'It seems the provided phone number includes characters
                which isn’t accepted by the site – please provide phone number
                without country code, spaces, or special characters.'
            );
        }
        return $errors;
    }

    /**
     * @param string $code
     * @param int $entityId
     * @return bool
     */
    protected function getMenuItemByCode($code, $entityId)
    {
        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), ['entity_id'])
            ->where('menu_item_code = ?', $code);

        $id = $this->getConnection()->fetchOne($select);
        if (!$id) {
            return false;
        }
        if ($id == $entityId) {
            return false;
        }
        return true;
    }

    /**
     * Get menu children by path and store
     *
     * @param string $path
     * @param int $storeId
     * @return []
     */
    protected function _getChildrenMenuItemsIds($path, $storeId = 0)
    {
        $connection = $this->getConnection();
        $infoTable = $this->_getMainInfoTable();
        $allIdsSelect = $connection->select()
            ->from($infoTable, ['id'])
            ->where($connection->quoteInto('path LIKE ?', $path . '/%'));
        if ($storeId > 0) {
            $allIdsSelect->where($connection->quoteInto('store_id = ?', $storeId));
        }
        return $connection->fetchCol($allIdsSelect);
    }

    /**
     * Get menu children by store
     *
     * @param \Digidirect\Navigation\Model\Menu $object
     * @param int $storeId
     * @return []
     */
    public function getChildrenIds($object, $storeId)
    {
        $dataDefault = $object->getDataDefault();
        $path = $object->getPath() ?? $dataDefault['path'] ?? null;
        if (!$path) {
            return [];
        }
        return $this->_getChildrenMenuItemsIds($object->getPath(), $storeId);
    }

    /**
     * @param AbstractModel $menu
     * @return void
     */
    protected function saveMenuItemStoreRelation(AbstractModel $menu)
    {
        $menuId = $menu->getId();
        if ($menuId) {
            $storeRelationTable = $this->getTable('digidirect_navigation_menu_item_store_relation');
            $setsIdsArray = $menu->getMenuStoreId();
            if (in_array(Store::DEFAULT_STORE_ID, $setsIdsArray)) {
                $setsIdsArray = [Store::DEFAULT_STORE_ID];
            }

            $connection = $this->getConnection();

            $select = $connection->select()
                ->from($storeRelationTable, ['menu_store_id'])
                ->where($connection->quoteInto('menu_entity_id = ?', $menuId));

            $oldSetIds = $connection->fetchCol($select, ['menu_store_id']);

            $insert = array_diff($setsIdsArray, $oldSetIds);
            $delete = array_diff($oldSetIds, $setsIdsArray);

            if (!empty($delete)) {
                foreach ($delete as $storeId) {
                    $condition = ['menu_entity_id = ?' => (int)$menuId, 'menu_store_id = ?' => (int)$storeId];
                    $connection->delete($storeRelationTable, $condition);
                }
            }

            if (!empty($insert)) {
                $ids = [];
                foreach ($setsIdsArray as $setId) {
                    $ids[] = ['menu_store_id' => $setId, 'menu_entity_id' => $menuId];
                }

                $this->getConnection()->insertOnDuplicate(
                    $storeRelationTable,
                    $ids
                );
            }
        }
    }

    /**
     * @param AbstractModel $object
     * @return array
     */
    protected function getMenuStoreIds(AbstractModel $object)
    {
        $select = $this->getConnection()->select()
            ->from('digidirect_navigation_menu_item_store_relation', ['menu_store_id'])
            ->where($this->getConnection()->quoteInto('menu_entity_id =?', $object->getId()));
        return $this->getConnection()->fetchCol($select, ['menu_store_id']);
    }
}
