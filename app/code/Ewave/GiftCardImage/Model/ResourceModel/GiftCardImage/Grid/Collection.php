<?php
namespace Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage\Grid;

use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * GiftCardImage Grid Collection
 * @package Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage\Grid
 */
class Collection extends \Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage\Collection
    implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * {@inheritdoc}
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_idFieldName = GiftCardImageInterface::ID;
        $this->_init(
            'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
            'Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage'
        );
    }
}
