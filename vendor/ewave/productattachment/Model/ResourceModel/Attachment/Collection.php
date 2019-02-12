<?php
namespace Ewave\ProductAttachment\Model\ResourceModel\Attachment;

use Ewave\ProductAttachment\Model\Attachment as AttachmentModel;
use Ewave\ProductAttachment\Model\ResourceModel\Attachment;
use Magento\Store\Model\Store;

/**
 * Class Collection
 * @package Ewave\ProductAttachment\Model\ResourceModel\Attachment
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $idFieldName = 'entity_id';

    /**
     * @var array
     */
    protected $addedTable = [];

    /**
     * @var \Ewave\ProductAttachment\Api\AttachmentRepositoryInterface
     */
    protected $attachmentRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var int
     */
    protected $productId;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ewave\ProductAttachment\Api\AttachmentRepositoryInterface $attachmentRepository
     * @param null $connection
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ewave\ProductAttachment\Api\AttachmentRepositoryInterface $attachmentRepository,
        $connection = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection);
        $this->attachmentRepository = $attachmentRepository;
        $this->storeManager = $storeManager;
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Ewave\ProductAttachment\Model\Attachment',
            'Ewave\ProductAttachment\Model\ResourceModel\Attachment'
        );
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->addStore();
        return parent::_afterLoad();
    }

    /**
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return $this
     */
    protected function addStore()
    {
        $linkedIds = $this->getColumnValues('entity_id');
        if ($this->productId && !empty($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['ar' => $this->getTable(Attachment::RELATION_TABLE)], ['attachment_id', 'store_id', 'attached'])
                ->where('ar.product_id = ?', $this->productId)
                ->order('ar.store_id');
            $result = $connection->fetchAll($select);
            $notAttachedStores = [];
            $storesByAttachment = [];
            $allStores = [];
            foreach ($this->storeManager->getStores() as $store) {
                $allStores[$store->getId()] = $store->getName();
            }
            foreach ($result as $item) {
                if (!empty($item['attached'])) {
                    $storesByAttachment[$item['attachment_id']][$item['store_id']] = $item['store_id'];
                } elseif ($item['store_id'] != AttachmentModel::ALL_STORE_ID) {
                    $notAttachedStores[$item['attachment_id']][$item['store_id']] = $item['store_id'];
                }
            }
            foreach ($storesByAttachment as $attachmentId => $item) {
                if (isset($notAttachedStores[$attachmentId])) {
                    $storeIds = array_keys(array_diff_key($allStores, array_flip($notAttachedStores[$attachmentId])));
                    $storesByAttachment[$attachmentId] = $storeIds;
                }
            }
            ksort($storesByAttachment);
            foreach ($this as $item) {
                if (isset($storesByAttachment[$item->getId()])) {
                    $stores = $storesByAttachment[$item->getId()];
                    if (current($stores) == AttachmentModel::ALL_STORE_ID) {
                        $item->setData('store_id', [AttachmentModel::ALL_STORE_ID]);
                    } else {
                        $item->setData('store_id', $storesByAttachment[$item->getId()]);
                    }
                }
            }
        }
        return $this;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function joinAttributes($storeId)
    {
        $attributes = [];
        foreach (AttachmentModel::getAttachmentAttributes() as $attribute) {
            $attributes[$attribute] =  sprintf('IFNULL(at_store.`%s`,at_def.`%s`)', $attribute, $attribute);
        }
        $this->getSelect()
            ->joinLeft(
                ['at_def' => Attachment::ATTRIBUTES_TABLE],
                'main_table.entity_id = at_def.attachment_id AND at_def.store_id = 0',
                $attributes
            )->joinLeft(
                ['at_store' => Attachment::ATTRIBUTES_TABLE],
                'main_table.entity_id = at_store.attachment_id AND at_store.store_id = ' . $storeId,
                []
            );
        return $this;
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return $this
     */
    public function addPositionToSelect($productId, $storeId)
    {
        $condition = new \Zend_Db_Expr(
            'main_table.entity_id = rel.attachment_id AND rel.store_id = '
            . (int)$storeId . ' AND rel.product_id = ' . (int)$productId
        );
        $this->getSelect()
            ->joinLeft(
                ['rel' => Attachment::RELATION_TABLE],
                $condition,
                ['position']
            );
        return $this;
    }

    /**
     * @param string $code
     * @param array $condition
     * @return $this
     */
    public function addFilterByAttribute($code, $condition)
    {
        foreach (AttachmentModel::getAttachmentAttributes() as $attribute) {
            if ($attribute == $code) {
                $alias = sprintf('IFNULL(at_def.`%s`,at_store.`%s`)', $attribute, $attribute);
                $whereCondition = $this->_getConditionSql($alias, $condition);
                $this->getSelect()->where($whereCondition);
            }
        }
        return $this;
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return $this
     */
    public function addFilterByProductId($productId, $storeId)
    {
        $recCondition = new \Zend_Db_Expr(
            'main_table.entity_id = rel.attachment_id AND rel.store_id = '
            . $storeId . ' AND rel.product_id = ' . $productId
        );
        $relDefCondition = new \Zend_Db_Expr(
            'main_table.entity_id = rel_def.attachment_id AND rel_def.store_id = '
            . AttachmentModel::ALL_STORE_ID . ' AND rel_def.product_id = ' . $productId
        );
        $this->getSelect()
            ->joinLeft(
                ['rel' => $this->getTable(Attachment::RELATION_TABLE)],
                $recCondition,
                ['attached_store' => 'rel.attached']
            )
            ->joinLeft(
                ['rel_def' => $this->getTable(Attachment::RELATION_TABLE)],
                $relDefCondition,
                ['attached_all' => 'rel_def.attached']
            )
            ->where('rel.product_id = ?', $productId)
            ->orWhere('rel_def.product_id = ?', $productId)
            ->order(['rel.position', 'rel_def.position', 'main_table.entity_id'])
        ;
        return $this;
    }

    /**
     * @return $this
     */
    public function groupByEntityId()
    {
        $this->getSelect()->group('main_table.entity_id');
        return $this;
    }

    /**
     * @return $this
     */
    public function orderByPosition()
    {
        $this->getSelect()->order(['rel.position', 'main_table.entity_id']);
        return $this;
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @param array $value
     * @return $this
     */
    public function addColumnFilterToCollection($productId, $storeId, $value)
    {
        $attachmentIds = $this->attachmentRepository->getRelationIds($productId, $storeId);
        if (empty($attachmentIds)) {
            $attachmentIds = 0;
        }
        if ($value) {
            $this->addFieldToFilter('main_table.entity_id', ['in' => $attachmentIds]);
        } elseif (!empty($attachmentIds)) {
            $this->addFieldToFilter('main_table.entity_id', ['nin' => $attachmentIds]);
        }
        return $this;
    }
}
