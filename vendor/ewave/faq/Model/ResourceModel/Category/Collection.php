<?php
namespace Ewave\Faq\Model\ResourceModel\Category;

use Ewave\Faq\Model\Category;
use Magento\Store\Model\Store;

/**
 * Class Collection
 * @package Ewave\Faq\Model\ResourceModel\Category
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @var int
     */
    protected $_storeViewId;

    /**
     * @var array
     */
    protected $_addedTable = [];

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Collection constructor.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_storeManager = $storeManager;

        if ($storeViewId = $this->_storeManager->getStore()->getId()) {
            $this->_storeViewId = $storeViewId;
        }
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Ewave\Faq\Model\Category', 'Ewave\Faq\Model\ResourceModel\Category');
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->_performAfterLoad('ewave_faq_category_store', 'category_id');
        return parent::_afterLoad();
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
        $linkedIds = $this->getColumnValues('entity_id');
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['faq_category_store' => $this->getTable($tableName)])
                ->where('faq_category_store.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData[$linkField]][] = $storeData['store_id'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData('entity_id');
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }
                    $storeIdKey = array_search(Store::DEFAULT_STORE_ID, $storesData[$linkedId], true);
                    if ($storeIdKey !== false) {
                        $stores = $this->_storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                        $storeCode = key($stores);
                    } else {
                        $storeId = current($storesData[$linkedId]);
                        $storeCode = $this->_storeManager->getStore($storeId)->getCode();
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_code', $storeCode);
                    $item->setData('store_id', $storesData[$linkedId]);
                }
            }
        }
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function addFilterByStoreId($storeId)
    {
        if (!is_array($storeId)) {
            $storeId = [$storeId];
        }
        $stores = array_merge([Store::DEFAULT_STORE_ID], $storeId);
        $this->getSelect()
            ->joinInner(
                ['fcs' => $this->getTable('ewave_faq_category_store')],
                'main_table.entity_id = fcs.category_id',
                []
            )
            ->where('fcs.store_id IN (?)', $stores)
            ->group('main_table.entity_id');
        return $this;
    }

    /**
     * @return $this
     */
    public function getCategoryCollection()
    {
        $storeId = $this->_storeManager->getStore(true)->getStoreId();
        $this->addFieldToFilter('status', Category::STATUS_ACTIVE)
            ->addFilterByStoreId($storeId)
            ->setOrder('ordering', 'ASC');
        return $this;
    }
}
