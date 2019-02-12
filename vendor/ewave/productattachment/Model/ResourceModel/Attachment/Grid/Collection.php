<?php
namespace Ewave\ProductAttachment\Model\ResourceModel\Attachment\Grid;

use Ewave\ProductAttachment\Model\ResourceModel\Attachment;
use Ewave\ProductAttachment\Model\Attachment as AttachmentModel;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Ewave\ProductAttachment\Model\ResourceModel\Attachment\Collection as AttachmentCollection;
use Magento\Store\Ui\Component\Listing\Column\Store;

/**
 * Class Collection
 * @package Ewave\ProductAttachment\Model\ResourceModel\Attachment\Grid
 */
class Collection extends AttachmentCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Ewave\ProductAttachment\Api\AttachmentRepositoryInterface $attachmentRepository
     * @param null $mainTable
     * @param string $eventPrefix
     * @param string $eventObject
     * @param string $resourceModel
     * @param null $connection
     * @param string $model
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ewave\ProductAttachment\Api\AttachmentRepositoryInterface $attachmentRepository,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $connection = null,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document'
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $attachmentRepository,
            $connection
        );

        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
        return $this;
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function _afterLoad()
    {
        $this->loadAttributes();
        return parent::_afterLoad();
    }

    /**
     * @return $this
     */
    protected function loadAttributes()
    {
        $linkedIds = $this->getColumnValues('entity_id');
        if (!empty($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['aa' => $this->getTable(Attachment::ATTRIBUTES_TABLE)])
                ->where('aa.attachment_id IN(?)', $linkedIds);
            $result = $connection->fetchAll($select);
            $loadedAttributes = [];
            $attachmentAttributes = AttachmentModel::getAttachmentAttributes();
            foreach ($result as $item) {
                foreach ($attachmentAttributes as $attribute) {
                    $loadedAttributes[$item['attachment_id']][$attribute][$item['store_id']] = $item[$attribute];
                }
            }
            foreach ($this as $item) {
                if (isset($loadedAttributes[$item->getEntityId()])) {
                    foreach ($attachmentAttributes as $attribute) {
                        $attributeValue = $loadedAttributes[$item->getEntityId()];
                        if (isset($attributeValue[$attribute])) {
                            ksort($attributeValue[$attribute]);
                            $item->setData($attribute, implode(', ', $attributeValue[$attribute]));
                        }
                    }
                }
            }
        }
        return $this;
    }
}
