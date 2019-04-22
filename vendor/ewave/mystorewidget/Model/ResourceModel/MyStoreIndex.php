<?php
namespace Ewave\MyStoreWidget\Model\ResourceModel;

use Ewave\MyStoreWidget\Helper\Config;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\DB\Select;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

/**
 * Class MyStoreIndex
 *
 * @package Ewave\MyStoreWidget\Model\ResourceModel
 */
class MyStoreIndex extends AbstractDb
{
    const TABLE_NAME = 'ewave_mystorewidget_store_index';

    /**
     * @var array
     */
    protected $rangeAttributes = [];

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * @var \Ewave\MyStoreWidget\Helper\SearchHelper
     */
    protected $searchHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * MyStoreIndex constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param Config $configHelper
     * @param \Ewave\MyStoreWidget\Helper\SearchHelper $searchHelper
     * @param string|null $connectionName
     * @param array $rangeAttributes
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Config $configHelper,
        \Ewave\MyStoreWidget\Helper\SearchHelper $searchHelper,
        $connectionName = null,
        $rangeAttributes = ['postcode'],
        StoreManagerInterface $storeManager = null
    ) {
        parent::__construct($context, $connectionName);
        $this->rangeAttributes = $rangeAttributes;
        $this->configHelper = $configHelper;
        $this->searchHelper = $searchHelper;
        $this->storeManager = $storeManager ?: ObjectManager::getInstance()->get(StoreManagerInterface::class);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'entity_id');
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getTableName($storeId = Store::DEFAULT_STORE_ID)
    {
        return $this->getTable(self::TABLE_NAME . '_' . $storeId);
    }

    /**
     * @param Select $select
     * @param array $fulltextColumns
     * @param int $storeId
     * @return void
     */
    public function createTableFromSelect(
        Select $select,
        array $fulltextColumns = [],
        $storeId = Store::DEFAULT_STORE_ID
    ) {
        $fulltextColumns = array_diff($fulltextColumns, $this->rangeAttributes);
        $connection = $this->getConnection();
        $table = $this->getTableName($storeId);
        $connection->dropTable($table);
        $connection->query("CREATE TABLE $table ENGINE=InnoDB $select");
        $tableStructure = $connection->describeTable($table);

        $columns = [];
        foreach ($fulltextColumns as $fulltextColumn) {
            if (isset($tableStructure[$fulltextColumn])
                && in_array($tableStructure[$fulltextColumn]['DATA_TYPE'], ['varchar', 'text'])
            ) {
                $columns[] = $fulltextColumn;
            }
        }

        if (!empty($columns)) {
            $connection->query("ALTER TABLE $table ADD FULLTEXT (" . implode(', ', $columns) . ")");
        }

        foreach ($this->rangeAttributes as $rangeAttribute) {
            if (isset($tableStructure[$rangeAttribute])) {
                $connection->query("ALTER TABLE $table ADD INDEX ($rangeAttribute)");
            }
        }
    }

    /**
     * @param string|integer|null $searchTerm
     * @param array $attributes
     * @param int|null $storeId
     * @return array
     * @throws \Zend_Db_Statement_Exception
     */
    public function searchStores($searchTerm = null, array $attributes = [], $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        $connection = $this->getConnection();
        $table = $this->getTableName($storeId);
        if (!$connection->query("SHOW TABLES LIKE '$table';")->rowCount()) {
            return [];
        }
        $query = $connection->select()->from($table, ['*', 'type' => '(\'\')']);
        if (!empty($attributes) && $searchTerm !== null) {
            $attributes = array_diff($attributes, $this->rangeAttributes);
            foreach ($this->rangeAttributes as $range) {
                if (in_array($range, $attributes)) {
                    continue;
                }
                $postcode = (int)$searchTerm;
                if ($postcode) {
                    $from = 'SUBSTRING_INDEX(' . $range . ', "-", 1)';
                    $to = 'SUBSTRING_INDEX(' . $range . ', "-", -1)';
                    $query->orWhere('? BETWEEN ' . $from . ' AND ' . $to, (int)$searchTerm);
                }
            }
            if ($attributes) {
                $query->orWhere(
                    'MATCH (' . implode(', ', $attributes) . ') AGAINST (? IN BOOLEAN MODE)',
                    '*' . $searchTerm . '*'
                );
            }
        }

        return $connection->fetchAll($query);
    }
}
