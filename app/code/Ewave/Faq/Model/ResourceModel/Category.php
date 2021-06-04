<?php
namespace Ewave\Faq\Model\ResourceModel;

use Ewave\Faq\Api\Data\CategoryInterface;
use Ewave\Faq\Model\Category as FaqCategory;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\Store;
use Magento\Framework\EntityManager\EntityManager;

/**
 * Class Category
 * @package Ewave\Faq\Model\ResourceModel
 */
class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const EWAVE_FAQ_CATEGORY_ITEM_INFO_TABLE = 'ewave_faq_category';

    /**
     * @var MetadataPool
     */
    protected $_metadataPool;

    /**
     * @var EntityManager
     */
    protected $_entityManager;

    /**
     * @param Context $context
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_entityManager = $entityManager;
        $this->_metadataPool = $metadataPool;
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('ewave_faq_category', 'entity_id');
    }

    /**
     * @inheritDoc
     */
    public function save(AbstractModel $object)
    {
        $this->_entityManager->save($object);
        $this->processAfterSaves($object);
        return $this;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $categoryId
     * @return array
     */
    public function lookupStoreIds($categoryId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->_metadataPool->getMetadata(CategoryInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $select = $connection->select()
            ->from(['cps' => $this->getTable('ewave_faq_category_store')], 'store_id')
            ->join(
                ['cp' => $this->getMainTable()],
                'cps.category_id = cp.' . $linkField,
                []
            )
            ->where('cp.' . $entityMetadata->getIdentifierField() . ' = :category_id');

        return $connection->fetchCol($select, ['category_id' => (int)$categoryId]);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param FaqCategory|AbstractModel $object
     * @return Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $entityMetadata = $this->_metadataPool->getMetadata(CategoryInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [
                Store::DEFAULT_STORE_ID,
                (int)$object->getStoreId(),
            ];
            $select->join(
                ['ewave_faq_category_store' => $this->getTable('ewave_faq_category_store')],
                $this->getMainTable() . '.' . $linkField . ' = ewave_faq_category_store.' . $linkField,
                []
            )
                ->where('is_active = ?', 1)
                ->where('ewave_faq_category_store.store_id IN (?)', $storeIds)
                ->order('ewave_faq_category_store.store_id DESC')
                ->limit(1);
        }

        return $select;
    }

    /**
     * Load an object
     *
     * @param FaqCategory|AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $categoryId = $this->getCategoryId($object, $value, $field);
        if ($categoryId) {
            $this->_entityManager->load($object, $categoryId);
        }
        return $this;
    }

    /**
     * @param AbstractModel $object
     * @param string $value
     * @param string|null $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws \Exception
     */
    private function getCategoryId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->_metadataPool->getMetadata(CategoryInterface::class);

        if (!is_numeric($value) && $field === null) {
            $field = 'identifier';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }

        $categoryId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $categoryId = count($result) ? $result[0] : false;
        }
        return $categoryId;
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
            $connection->quoteInto('entity_id IN (?)', $ids)
        );
    }

    /**
     * Get main info table
     *
     * @return string
     */
    protected function _getMainInfoTable()
    {
        return $this->getTable(self::EWAVE_FAQ_CATEGORY_ITEM_INFO_TABLE);
    }

    /**
     * @param string $title
     * @return string
     * @throws LocalizedException
     */
    public function loadByTitle($title)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from($this->getMainTable())->where('title = ?', $title);
        return $connection->fetchRow($select);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $categoryId
     * @return array
     */
    public function lookupQuestionIds($categoryId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->_metadataPool->getMetadata(CategoryInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $select = $connection->select()
            ->from(['fcr' => $this->getTable('ewave_faq_category_relation')], 'faq_id')
            ->join(
                ['c' => $this->getMainTable()],
                'fcr.category_id = c.' . $linkField,
                []
            )
            ->where('c.' . $entityMetadata->getIdentifierField() . ' = :category_id');

        return $connection->fetchCol($select, ['category_id' => (int)$categoryId]);
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    public function loadLastPosition()
    {
        $select = $this->getConnection()->select()->from($this->getMainTable(), 'ordering')->order('ordering DESC');
        return (int)$this->getConnection()->fetchOne($select);
    }

    /**
     * @param int $position
     * @return array
     * @throws LocalizedException
     */
    public function loadByPosition($position)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getMainTable(), 'entity_id')->where('ordering = ?', (int)$position);
        return $this->getConnection()->fetchRow($select);
    }
}
