<?php
namespace Digidirect\AbstractAttributesNavigation\Model\ResourceModel;

use Digidirect\Navigation\Model\ResourceModel\Type\SaveProcessorInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Digidirect\Navigation\Model\Menu as MenuItem;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Processor
 * @package Digidirect\AbstractAttributesNavigation\Model\ResourceModel
 */
class Processor extends AbstractDb implements SaveProcessorInterface
{
    /**
     * @var array
     */
    protected $tableConfig;

    /**
     * Processor constructor.
     * @param Context $context
     * @param array $data
     * @param null $connectionName
     */
    public function __construct(Context $context, array $data = [], $connectionName = null)
    {
        $this->tableConfig = $data;
        parent::__construct($context, $connectionName = null);
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
                    ($useDefaultData[$fieldName] ? null : $this->_getFieldData($menu, $fieldName))
                    : $this->_getFieldData($menu, $fieldName);
            }
            $info[$storeId]['store_id'] = $storeId;
            $info[$storeId]['menu_item_id'] = $menu->getId();
        }

        return $info;
    }

    /**
     * @param MenuItem $menu
     * @param $fieldName
     * @return string
     */
    protected function _getFieldData(MenuItem $menu, $fieldName)
    {
        $fieldValue = $menu->getData($fieldName);
        return is_array($fieldValue) ? implode(',', $fieldValue) : $fieldValue;
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
     * @param int $entityId
     * @return array
     */
    public function getMenuIdByTargetEntityId($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), ['menu_item_id'])
            ->where($connection->quoteInto('attribute_id =? ', $entityId));
        return $connection->fetchCol($select);
    }
}
