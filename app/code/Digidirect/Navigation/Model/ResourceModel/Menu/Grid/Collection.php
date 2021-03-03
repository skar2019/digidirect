<?php
namespace Digidirect\Navigation\Model\ResourceModel\Menu\Grid;

use Digidirect\Navigation\Helper\Data;
use Digidirect\Navigation\Model\Config\Source\Type;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Digidirect\Navigation\Setup\InstallSchema;
use Magento\Store\Model\Store;
use Digidirect\Navigation\Model\ResourceModel\Menu\JoinTypeInterface;

/**
 * Class Collection
 *
 * @package Digidirect\Navigation\Model\ResourceModel\Menu\Grid
 */
class Collection extends \Digidirect\Navigation\Model\ResourceModel\Menu\Collection
{
    /**
     * @var array
     */
    protected $_joinCallback = [];

    /**
     * @var array
     */
    protected $_selectJoinCallback = [];

    /**
     * Collection constructor.
     *
     * @param EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param Data $helper
     * @param Type $menuItemTypes
     * @param null $connection
     * @param null $resource
     * @param array $tablesData
     * @param array $joinProcessors
     * @param array $joinCallback
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Data $helper,
        Type $menuItemTypes,
        $connection = null,
        $resource = null,
        array $tablesData = [],
        array $joinProcessors = [],
        array $joinCallback = []
    ) {
        $this->_joinCallback = $joinCallback;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $helper,
            $menuItemTypes,
            $connection,
            $resource,
            $tablesData,
            $joinProcessors
        );
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $allIds = $this->getColumnValues('entity_id');
        if (!empty($allIds)) {

            $connection = $this->getConnection();
            $select = $connection->select()
                ->from($this->getTable('digidirect_navigation_menu_item_store_relation'),
                    ['menu_entity_id', 'menu_store_id'])
                ->where($connection->quoteInto('menu_entity_id IN (?)', $allIds));
            $result = $connection->fetchAll($select);

            $storeData = [];
            if (!empty($result)) {
                foreach ($result as $info) {
                    $storeData[$info['menu_entity_id']][] = $info['menu_store_id'];
                }
            }
            foreach ($this as $item) {
                $item->setData('store_id', $storeData[$item->getId()] ?? null);
            }
        }
        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'store_id') {
            return $this;
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * @param null $storeId
     * @return $this
     */
    public function addStoreFilter($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->helper->getCurrentStoreId();
            $storeIds = [$storeId];
        } else {
            if (is_array($storeId)) {
                $storeIds = $storeId;
                $storeIds[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            } else {
                $storeIds = [$storeId];
            }
        }

        $this->getSelect()->joinLeft(
            ['mst' => 'digidirect_navigation_menu_item_store_relation'],
            'main_table.entity_id = mst.menu_entity_id',
            ['menu_store_id' => 'mst.menu_store_id']
        )->where(
            $this->getConnection()->quoteInto('mst.menu_store_id IN (?) ', $storeIds)
        )->group('main_table.entity_id');
        return $this;
    }

    /**
     * Join attributes table
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $aCallBack = $this->_joinCallback ?? [];
        if (!empty($aCallBack)) {
            foreach ($aCallBack as $callback) {
                if (isset($callback) && is_callable([$this, $callback])) {
                    $this->$callback();
                }
            }
        }

        $this->_joinProcessorsDefault(Store::DEFAULT_STORE_ID);
        return $this;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    protected function _joinProcessorsDefault($storeId)
    {
        if (!empty($this->joinProcessors)) {
            foreach ($this->joinProcessors as $processor) {
                if (!($processor instanceof JoinTypeInterface)) {
                    continue;
                }
                $tableAlias = $processor->getTableAlias();
                $fieldsToSelect = [];
                foreach ($processor->getFields() as $field) {
                    $fieldsToSelect[$processor->getFieldPrefix() . $field] = $tableAlias . '.' . $field;
                }
                $this->getSelect()->joinLeft(
                    [$tableAlias => $this->getTable($processor->getTable())],
                    'main_table.entity_id = ' . $tableAlias . '.menu_item_id AND ' . $tableAlias . '.store_id =' . $storeId,
                    $fieldsToSelect
                );
            }
        }
        return $this;
    }

    /**
     * Get menu set id by join table
     *
     * @return $this
     */
    protected function _joinSetLink()
    {
        $tableAlias = 'menu_item_set_link';
        $selectJoin = new \Zend_Db_Expr("GROUP_CONCAT(menu_item_set_link.menu_set_id SEPARATOR ',') AS menu_set_id");
        $this->getSelect()
            ->joinLeft(
                [
                    $tableAlias => $this->getTable(
                        InstallSchema::DIGIDIRECT_NAVIGATION_MENU_SET_LINK
                    )
                ],
                'main_table.entity_id = ' . $tableAlias . '.menu_entity_id',
                [$selectJoin]
            )
            ->group('main_table.entity_id');
        $this->_selectJoinCallback[$tableAlias] = $selectJoin;
        return $this;
    }

    /**
     * Join store info fields
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        parent::_renderFiltersBefore();
        if ($this->getStoreId() != Store::DEFAULT_STORE_ID && !empty($this->_selectJoinCallback)) {
            foreach ($this->_selectJoinCallback as $tableAlias => $selectColumn) {
                $this->getSelect()->columns(
                    $selectColumn,
                    $tableAlias
                );
            }
        }
    }
}
