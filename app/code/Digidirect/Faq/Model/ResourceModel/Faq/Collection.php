<?php
namespace Digidirect\Faq\Model\ResourceModel\Faq;

use Magento\Store\Model\Store;

/**
 * Class Collection
 * @package Digidirect\Faq\Model\ResourceModel\Category
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
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_readConnection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param null $connection
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection);
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
        $this->_init('Digidirect\Faq\Model\Faq', 'Digidirect\Faq\Model\ResourceModel\Faq');
    }

    /**
     * @return int
     */
    public function getStoreViewId()
    {
        return $this->_storeViewId;
    }

    /**
     * @param int $storeViewId
     * @return $this
     */
    public function setStoreViewId($storeViewId)
    {
        $this->_storeViewId = $storeViewId;
        return $this;
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->_loadRelatedCategories();
        $this->_loadTags();
        return parent::_afterLoad();
    }

    /**
     * @return $this
     */
    protected function _loadTags()
    {
        $tableName = 'digidirect_faq_tag_relation';
        $linkField = 'faq_id';
        $linkedIds = $this->getColumnValues('entity_id');
        if (!empty($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['digidirect_tag_relation' => $this->getTable($tableName)], [])
                ->joinLeft(
                    ['tag' => $this->getTable('digidirect_faq_tag')],
                    'digidirect_tag_relation.tag_id = tag.entity_id',
                    ['tag_name' => 'title']
                )
                ->where('digidirect_tag_relation.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);
            $tags = [];
            foreach ($result as $tagData) {
                $tags[] = $tagData['tag_name'];
            }
            foreach ($this as $item) {
                $item->setData('tags', implode(',', $tags));
            }
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function _loadRelatedCategories()
    {
        $tableName = 'digidirect_faq_category_relation';
        $linkField = 'faq_id';
        $linkedIds = $this->getColumnValues('entity_id');
        if (!empty($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['rel' => $this->getTable($tableName)])
                ->joinLeft('digidirect_faq_category as c', 'c.entity_id = rel.category_id', ['title'])
                ->where('rel.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);

            if ($result) {
                $categoryData = [];
                $categoryTitles = [];
                foreach ($result as $item) {
                    $categoryData[$item[$linkField]][] = $item['category_id'];
                    $categoryTitles[$item[$linkField]][] = $item['title'];
                }
                foreach ($this as $item) {
                    $linkedId = $item->getData('entity_id');
                    if (!isset($categoryData[$linkedId])) {
                        continue;
                    }

                    $item->setData('category_id', $categoryData[$linkedId]);
                    if (isset($categoryTitles[$linkedId])) {
                        $item->setData('categories_title', implode(', ', $categoryTitles[$linkedId]));
                    }
                }
            }
        }
        return $this;
    }

    /**
     * @param int|array $ids
     * @return $this
     */
    public function addFilterByCategory($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $this->getSelect()
            ->joinInner(
                ['rel' => $this->getTable('digidirect_faq_category_relation')],
                'rel.faq_id = main_table.entity_id',
                []
            )
            ->where('rel.category_id IN (?)', $ids);
        return $this;
    }

    /**
     * @param int|array $ids
     * @return $this
     */
    public function addFilterByTag($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $this->getSelect()
            ->joinInner(['rel' => $this->getTable('digidirect_faq_tag_relation')], 'rel.faq_id = main_table.entity_id', [])
            ->where('rel.tag_id IN (?)', $ids);
        return $this;
    }
}
