<?php
namespace Digidirect\Faq\Model\ResourceModel;

use Digidirect\Faq\Api\Data\FaqInterface;
use Digidirect\Faq\Model\Faq as FaqItem;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\EntityManager\EntityManager;

/**
 * Class Faq
 * @package Digidirect\Faq\Model\ResourceModel
 */
class Faq extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const DIGIDIRECT_FAQ_FAQ_ITEM_INFO_TABLE = 'digidirect_faq';

    /**
     * @var EntityManager
     */
    protected $_entityManager;

    /**
     * @var MetadataPool
     */
    protected $_metadataPool;

    /**
     * Faq constructor.
     * @param Context $context
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param null $connectionName
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
        $this->_init('digidirect_faq', 'entity_id');
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
     * Load an object
     *
     * @param FaqItem|AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $categoryId = $this->getFaqId($object, $value, $field);
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
    private function getFaqId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->_metadataPool->getMetadata(FaqInterface::class);

        if (!is_numeric($value) && $field === null) {
            $field = 'identifier';
        } elseif (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }

        $faqId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $faqId = count($result) ? $result[0] : false;
        }
        return $faqId;
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
        return $this->getTable(self::DIGIDIRECT_FAQ_FAQ_ITEM_INFO_TABLE);
    }

    /**
     * @param int $faqId
     * @return array
     * @throws LocalizedException
     * @throws \Exception
     */
    public function lookupCategoryIds($faqId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->_metadataPool->getMetadata(FaqInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $select = $connection->select()
            ->from(['fcr' => $this->getTable('digidirect_faq_category_relation')], 'category_id')
            ->join(
                ['f' => $this->getMainTable()],
                'fcr.faq_id = f.' . $linkField,
                []
            )
            ->where('f.' . $entityMetadata->getIdentifierField() . ' = :faq_id');
        return $connection->fetchCol($select, ['faq_id' => (int)$faqId]);
    }

    /**
     * @param int $faqId
     * @return array
     * @throws LocalizedException
     * @throws \Exception
     */
    public function lookupTags($faqId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->_metadataPool->getMetadata(FaqInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $select = $connection->select()
            ->from(['ftr' => $this->getTable('digidirect_faq_tag_relation')], 'tag_id')
            ->join(
                ['f' => $this->getMainTable()],
                'ftr.faq_id = f.' . $linkField,
                []
            )
            ->join(['tag' => $this->getTable('digidirect_faq_tag')], 'ftr.tag_id = tag.entity_id', ['tag_name' => 'title'])
            ->where('f.' . $entityMetadata->getIdentifierField() . ' = :faq_id');
        return $connection->fetchPairs($select, ['faq_id' => (int)$faqId]);
    }

    /**
     * @param int $faqId
     * @return array
     * @throws LocalizedException
     * @throws \Exception
     */
    public function lookupTagIds($faqId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->_metadataPool->getMetadata(FaqInterface::class);
        $linkField = $entityMetadata->getLinkField();
        $select = $connection->select()
            ->from(['ftr' => $this->getTable('digidirect_faq_tag_relation')], 'tag_id')
            ->join(
                ['f' => $this->getMainTable()],
                'ftr.faq_id = f.' . $linkField,
                []
            )->where('f.' . $entityMetadata->getIdentifierField() . ' = :faq_id');
        return $connection->fetchCol($select, ['faq_id' => (int)$faqId]);
    }
}
