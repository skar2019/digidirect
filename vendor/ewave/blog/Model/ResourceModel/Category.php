<?php

namespace Ewave\Blog\Model\ResourceModel;

use Ewave\Blog\Api\Data\PostInterface;
use Ewave\Blog\Model\Config\Provider\Status;
use Ewave\Blog\Model\CurrentStoreFetcher;
use Ewave\Blog\Sql\Aliases;
use Ewave\Blog\Sql\CategoryInformationJoin;
use Ewave\Blog\Sql\CategoryInformationSave;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Category
 */
class Category extends AbstractDb
{
    const STORE_RELATION_TABLE = 'ewave_blog_category_stores';
    const EWAVE_BLOG_CATEGORY_TABLE = 'ewave_blog_category';
    const EWAVE_BLOG_CATEGORY_INFORMATION_TABLE = 'ewave_blog_category_information';

    /**
     * @var CurrentStoreFetcher
     */
    protected $storeFetcher;

    /**
     * @var CategoryInformationJoin
     */
    protected $joinStoreInformation;

    /**
     * @var CategoryInformationSave
     */
    protected $saveStoreInformation;

    /**
     * Category constructor.
     *
     * @param Context $context
     * @param CurrentStoreFetcher $currentStoreFetcher
     * @param CategoryInformationJoin $categoryInformationJoin
     * @param CategoryInformationSave $categoryInformationSave
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        CurrentStoreFetcher $currentStoreFetcher,
        CategoryInformationJoin $categoryInformationJoin,
        CategoryInformationSave $categoryInformationSave,
        $connectionName = null
    ) {
        $this->saveStoreInformation = $categoryInformationSave;
        $this->joinStoreInformation = $categoryInformationJoin;
        $this->storeFetcher = $currentStoreFetcher;
        parent::__construct($context, $connectionName);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::EWAVE_BLOG_CATEGORY_TABLE, 'entity_id');
    }

    /**
     * @param string $categoryId
     * @param array $newStores
     * @return void
     */
    public function updateStores($categoryId, array $newStores)
    {
        $connection = $this->getConnection();
        $oldStores = $this->lookupStoreIds((int)$categoryId);
        $table = $this->getTable(self::STORE_RELATION_TABLE);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = [
                'category_id = ?' => (int)$categoryId,
                'store_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newStores, $oldStores);
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'category_id' => (int)$categoryId,
                    'store_id' => (int)$storeId,
                ];
            }
            $connection->insertMultiple($table, $data);
        }
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $categoryId
     * @return array
     */
    public function lookupStoreIds($categoryId)
    {
        $select = $this->getStoreRelationSelect();
        $select->where(Aliases::CATEGORY_ENTITY_TABLE_ALIAS . '.entity_id = :category_id');
        return $this->getConnection()->fetchCol($select, ['category_id' => (int)$categoryId]);
    }

    /**
     * @param string $urlKey
     * @param array $storeId
     * @return string|bool
     */
    public function loadCategoryIdByUrlKey($urlKey, array $storeId)
    {
        $connection = $this->getConnection();
        $select = $this->getStoreRelationSelect();
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('category_id');
        $urlKeyExpr = sprintf(Category::EWAVE_BLOG_CATEGORY_INFORMATION_TABLE . '.url_key = "%s"', $urlKey);
        $expr = $this->getConnection()->getIfNullSql(
            $urlKeyExpr,
            CategoryInformationJoin::DEFAULT_STORE_COLUMN_PREFIX . $urlKeyExpr
        );
        $select->where(
            $expr
        );
        $select->where('bcs.store_id IN (?)', $storeId);
        return $connection->fetchOne($select);
    }

    /**
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getStoreRelationSelect()
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['bcs' => self::STORE_RELATION_TABLE], 'store_id')
            ->join(
                [Aliases::CATEGORY_ENTITY_TABLE_ALIAS => $this->getMainTable()],
                'bcs.category_id =' . Aliases::CATEGORY_ENTITY_TABLE_ALIAS . ' .entity_id',
                [Aliases::CATEGORY_ENTITY_TABLE_ALIAS . '.entity_id']
            );
        $this->joinStoreInformation->join($select, $this->storeFetcher->getCurrentStoreId());
        $this->joinStoreInformation->joinDefault($select);
        return $select;
    }

    /**
     * @param int $categoryId
     * @return string
     */
    public function getCountPostsByCategoryId($categoryId)
    {
        $connection = $this->getConnection();
        $select = $connection
            ->select()
            ->from(['rel' => Post::CATEGORY_RELATION_TABLE], new \Zend_Db_Expr('COUNT(rel.post_id)'))
            ->joinLeft(
                ['post' => $this->getTable(PostInterface::EWAVE_BLOG_POST_TABLE)],
                'rel.post_id = post.entity_id',
                []
            )
            ->where('post.status = ?', Status::STATUS_ENABLED)
            ->where('post.publish_date <= NOW()')
            ->where('rel.category_id = ?', (int)$categoryId);
        return $connection->fetchOne($select);
    }

    /**
     * @param int $id
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadById($id)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable())->where('entity_id = ?', (int)$id);
        return $connection->fetchRow($select);
    }

    /**
     * @param int $categoryId
     * @param int $parentId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateParentId($categoryId, $parentId)
    {
        return $this->getConnection()->update(
            $this->getMainTable(),
            ['parent_id' => $parentId],
            ['parent_id = ?' => $categoryId]
        );
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $storeId = $this->storeFetcher->getCurrentStoreId();
        $select = $this->joinStoreInformation->join($select, $storeId, $this->getMainTable(), $value);
        return $select;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_afterSave($object);
        $this->saveStoreInformation->saveInformation($object);
        return $this;
    }
}
