<?php

namespace Digidirect\Navigation\Model\ResourceModel\Set;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'set_id';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metaDataPool;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        $this->metaDataPool = $metadataPool;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Digidirect\Navigation\Model\Set', 'Digidirect\Navigation\Model\ResourceModel\Set');
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * @param int $storeId
     * @return []
     */
    public function getSetsByStore($storeId)
    {
        $this->addFieldToFilter('store_id', ['in' => [\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int)$storeId]]);
        return $this->toOptionArray();
    }

    /**
     * Add store filter
     *
     * @param int $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->performAddStoreFilter($storeId);
        return $this;
    }

    /**
     * Add field filter to collection
     *
     * @param array|string $field
     * @param string|int|array|null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->performAddStoreFilter($condition, false);
        }

        parent::addFieldToFilter($field, $condition);
        return $this;
    }

    /**
     * Perform adding filter by store
     *
     * @param int|array|Store $store
     * @param bool $withAdmin
     * @return $this
     */
    protected function performAddStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store', ['in' => $store], 'public');
        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $entityMetadata = $this->metaDataPool->getMetadata(\Digidirect\Navigation\Api\Data\SetInterface::class);
        $this->joinStoreRelationTable('digidirect_navigation_menu_set_store', $entityMetadata->getLinkField());
    }

    /**
     * Add join to store table
     *
     * @param string $tableName
     * @param string $linkField
     * @return void
     */
    protected function joinStoreRelationTable($tableName, $linkField)
    {
        if ($this->getFilter('store')) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable($tableName)],
                'main_table.' . $linkField . ' = store_table.menu_set_id',
                []
            )->group(
                'main_table.' . $linkField
            );
        }
        parent::_renderFiltersBefore();
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->_performAfterLoad('digidirect_navigation_menu_set_store', 'menu_set_id');
        parent::_afterLoad();
        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function _performAfterLoad($tableName, $linkField)
    {
        $linkedIds = $this->getColumnValues('set_id');
        if (!empty($linkedIds)) {

            $connection = $this->getConnection();
            $select = $connection->select()->from(['digidirect_navigation_menu_set_store' => $this->getTable($tableName)])
                ->where('digidirect_navigation_menu_set_store.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);

            $storeData = [];
            if (!empty($result)) {
                foreach ($result as $info) {
                    $storeData[$info['menu_set_id']][] = $info['store_id'];
                }
            }
            foreach ($this as $item) {
                $item->setData('store_id', $storeData[$item->getId()] ?? null);
            }
        }
    }
}
