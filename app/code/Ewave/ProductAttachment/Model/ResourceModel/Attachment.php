<?php
namespace Ewave\ProductAttachment\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\Store;
use Magento\Framework\EntityManager\EntityManager;
use Ewave\ProductAttachment\Helper\Data;
use Ewave\ProductAttachment\Model\Attachment as AttachmentModel;

/**
 * Class Attachment
 * @package Ewave\ProductAttachment\Model\ResourceModel
 */
class Attachment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const ATTRIBUTES_TABLE = 'ewave_product_attachment_attributes';
    const RELATION_TABLE = 'ewave_product_attachment_relation';

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Attachment constructor.
     * @param Context $context
     * @param MetadataPool $metadataPool
     * @param EntityManager $entityManager
     * @param Data $helper,
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        EntityManager $entityManager,
        Data $helper,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->metadataPool = $metadataPool;
        $this->entityManager = $entityManager;
        $this->helper = $helper;
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('ewave_product_attachment', 'entity_id');
    }

    /**
     * @inheritDoc
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }
    
    /**
     * @param int $entityId
     * @param string $filePath
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateFilePath($entityId, $filePath)
    {
        $connection = $this->getConnection();
        return $connection->update(
            $this->getMainTable(),
            [AttachmentModel::FILE_PATH => $filePath],
            [AttachmentModel::ENTITY_ID . ' = ?' => $entityId]
        );
    }

    /**
     * @param array $data
     * @return $this
     */
    public function updateAttachmentAttributes(array $data)
    {
        $connection = $this->getConnection();
        if (!empty($data[AttachmentModel::ENTITY_ID])) {
            $connection->update(
                $this->getTable(self::ATTRIBUTES_TABLE),
                $data,
                [AttachmentModel::ENTITY_ID . ' = ?' => $data[AttachmentModel::ENTITY_ID]]
            );
        } else {
            $connection->insert($this->getTable(self::ATTRIBUTES_TABLE), $data);
        }
        return $this;
    }

    /**
     * @param int $attachmentId
     * @param int $storeId
     * @return array
     */
    public function loadAttachmentAttributes($attachmentId, $storeId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable(self::ATTRIBUTES_TABLE)
        )->where('attachment_id = ?', $attachmentId)
        ->where('store_id = ?', $storeId);
        $result = $connection->fetchRow($select);
        return is_array($result) ? $result : [];
    }

    /**
     * @param AbstractModel $object
     * @param mixed $attachmentId
     * @param null $field
     * @return mixed
     */
    public function load(AbstractModel $object, $attachmentId, $field = null)
    {
        return $this->entityManager->load($object, $attachmentId);
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @param array $attachments
     * @param array $uncheckedData
     * @return bool
     */
    public function saveProductRelation($productId, $storeId, array $attachments, array $uncheckedData = [])
    {
        $connection = $this->getConnection();
        $dataWasChanged = false;

        if (empty($attachments)) {
            $connection->update(
                $this->getTable(self::RELATION_TABLE),
                ['attached' => AttachmentModel::NOT_ATTACHED_FLAG],
                ['product_id = ?' => $productId, 'store_id = ?' => $storeId]
            );
        } else {
            $currentAttachments = $this->loadProductAttachments($productId, $storeId);
            $needDeleteIds = array_diff_key($currentAttachments, $attachments);
            $needUpdatePosition = array_diff(array_values($attachments), array_values($currentAttachments));
            $needUpdateFlag = array_diff_key($attachments, $currentAttachments);
            if (!empty($needUpdatePosition) || !empty($needUpdateFlag)) {
                foreach ($attachments as $attachmentId => $position) {
                    $saveData = [
                        'attachment_id' => $attachmentId,
                        'product_id' => $productId,
                        'store_id' => $storeId,
                        'position' => (int)$position,
                        'attached' => AttachmentModel::ATTACHED_FLAG
                    ];
                    $connection->insertOnDuplicate(self::RELATION_TABLE, $saveData, ['position', 'attached']);
                }
                $dataWasChanged = true;
            }

            if (!empty($needDeleteIds)) {
                $connection->update(
                    self::RELATION_TABLE,
                    ['attached' => AttachmentModel::NOT_ATTACHED_FLAG],
                    [
                        'product_id = ?' => (int)$productId,
                        'store_id = ?' => $storeId,
                        'attachment_id IN(?)' => array_keys($needDeleteIds)
                    ]
                );
                $dataWasChanged = true;
            }
        }

        if (!empty($uncheckedData)) {
            foreach ($uncheckedData as $uncheckedAttachmentId) {
                $uncheckedAttachmentId = (int)$uncheckedAttachmentId;
                if (!empty($uncheckedAttachmentId)) {
                    $saveData = [
                        'attachment_id' => $uncheckedAttachmentId,
                        'product_id' => $productId,
                        'store_id' => $storeId,
                        'attached' => AttachmentModel::NOT_ATTACHED_FLAG
                    ];
                    $connection->insertOnDuplicate(self::RELATION_TABLE, $saveData, ['attached']);
                }
            }
            $dataWasChanged = true;
        }

        return $dataWasChanged;
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function loadProductAttachments($productId, $storeId)
    {
        $select = $this->getRelationSelect($productId, $storeId, ['attachment_id', 'position']);
        $select->where('attached = ?', AttachmentModel::ATTACHED_FLAG);
        return $this->getConnection()
            ->fetchPairs($select);
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @param int $field
     * @param int $attached
     * @return array
     */
    public function loadRelationData($productId, $storeId, $field, $attached = AttachmentModel::ATTACHED_FLAG)
    {
        $select = $this->getConnection()->select()
            ->from(self::RELATION_TABLE, [$field])
            ->where('product_id = ?', $productId)
            ->where('store_id = ?', $storeId)
            ->where('attached = ?', $attached)
            ->order('position')
            ->group($field);
        return $this->getConnection()->fetchCol($select);
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return Select
     */
    public function loadAllProductAttachmentsByStore($productId, $storeId)
    {
        return $this->getConnection()->fetchAll($this->getRelationSelect($productId, $storeId));
    }
    
    /**
     * @param int $productId
     * @param int $storeId
     * @param array $columns
     * @return Select
     */
    protected function getRelationSelect($productId, $storeId, $columns = ['*'])
    {
        return $this->getConnection()->select()
            ->from(self::RELATION_TABLE, $columns)
            ->where('product_id = ?', $productId)->where('store_id = ?', $storeId)
            ->order('position');
    }
}
