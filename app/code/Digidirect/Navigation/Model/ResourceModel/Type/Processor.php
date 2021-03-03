<?php
namespace Digidirect\Navigation\Model\ResourceModel\Type;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Digidirect\Navigation\Model\Menu as MenuItem;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Digidirect\Navigation\Model\Menu;

/**
 * Interface SaveProcessorInterface
 * @package Digidirect\Navigation\Model\ResourceModel\Type
 */
class Processor extends AbstractDb implements SaveProcessorInterface
{
    /**
     * @var array
     */
    protected $tableConfig;

    /**
     * ProcessorAbstract constructor.
     * @param Context $context
     * @param array $tableConfig
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        array $tableConfig = [],
        $connectionName = null
    ) {
        $this->tableConfig = $tableConfig;
        parent::__construct($context, $connectionName);
    }

    /**
     * Set main table
     * @return void
     */
    protected function _construct()
    {
        $this->_setMainTable($this->tableConfig['name']);
    }

    /**
     * @param MenuItem $menu
     * @return array
     */
    protected function _prepareData(MenuItem $menu)
    {
        $storeIds = $menu->getStores();
        $useDefaultData = $menu->getUseDefault();
        $canUseDefault = !empty($useDefaultData);

        $info = [];
        foreach ($storeIds as $storeId) {
            foreach ($this->_getFields() as $fieldName) {
                $info[$storeId][$fieldName] = $canUseDefault ?
                    ($useDefaultData[$fieldName] ? null : $menu->getData($fieldName))
                    : $menu->getData($fieldName);
            }
            $info[$storeId]['store_id'] = $storeId;
            $info[$storeId]['menu_item_id'] = $menu->getId();
        }

        return $info;
    }

    /**
     * @return []
     */
    protected function _getFields()
    {
        return $this->tableConfig['fields'];
    }

    /**
     * @param string $table
     * @param array $info
     * @return int
     */
    protected function _updateTypeTable(string $table, array $info)
    {
        return $this->getConnection()->insertOnDuplicate($table, $info);
    }

    /**
     * @param MenuItem $menuItem
     * @return MenuItem
     */
    public function saveInformation(\Digidirect\Navigation\Model\Menu $menuItem)
    {
        $information = $this->_prepareData($menuItem);
        $this->_updateTypeTable($this->getMainTable(), $information);
        return $menuItem;
    }

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param string $mainTable
     * @param int $storeId
     * @return \Magento\Framework\DB\Select
     */
    public function getJoinRule(\Magento\Framework\DB\Select $select, string $mainTable, $storeId)
    {
        $mainTable = 'digidirect_navigation_menu_entity';
        $select->joinLeft(
            ['link_information_table' => $this->getMainTable()],
            $mainTable
            . '.entity_id = link_information_table.menu_item_id AND link_information_table.store_id = ' . $storeId,
            ['link_info' => '*']
        );
        return $select;
    }

    /**
     * @param array $entityIds
     * @param string $field
     * @return array
     */
    public function getMenuIdByTargetEntityId(array $entityIds, $field)
    {
        try {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['main_table' => $this->getMainTable()], ['menu_item_id'])
                ->where($connection->quoteInto('main_table.' . $field . ' IN (?) ', $entityIds))
                ->joinInner(
                    ['info_table' => $this->getTable('digidirect_navigation_menu_item_info')],
                    'main_table.menu_item_id = info_table.id AND info_table.status = ' . Menu::MENU_ITEM_STATUS_ENABLED,
                    []
                );
            return $connection->fetchCol($select);
        } catch (LocalizedException $e) {
            return [];
        }
    }
}
