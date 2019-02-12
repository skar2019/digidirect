<?php
namespace Ewave\Navigation\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteriaInterface;
use Ewave\Navigation\Api\Data;
use Ewave\Navigation\Api\SetRepositoryInterface;
use Ewave\Navigation\Model\ResourceModel\Set as ResourceSet;
use Ewave\Navigation\Model\ResourceModel\Set\CollectionFactory as SetCollectionFactory;

/**
 * Class SetRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SetRepository implements SetRepositoryInterface
{
    /**
     * @var ResourceSet
     */
    protected $resource;

    /**
     * @var SetFactory
     */
    protected $setFactory;

    /**
     * @var SetCollectionFactory
     */
    protected $setCollectionFactory;

    /**
     * @var Data\SetSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Ewave\Navigation\Api\Data\SetInterfaceFactory
     */
    protected $dataSetFactory;

    /**
     * SetRepository constructor.
     * @param ResourceSet $resource
     * @param SetFactory $setFactory
     * @param Data\SetInterfaceFactory $dataSetFactory
     * @param SetCollectionFactory $setCollectionFactory
     * @param Data\SetSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        ResourceSet $resource,
        SetFactory $setFactory,
        \Ewave\Navigation\Api\Data\SetInterfaceFactory $dataSetFactory,
        SetCollectionFactory $setCollectionFactory,
        Data\SetSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resource = $resource;
        $this->setFactory = $setFactory;
        $this->setCollectionFactory = $setCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataSetFactory = $dataSetFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Save Set data
     *
     * @param \Ewave\Navigation\Api\Data\SetInterface $set
     * @return Data\SetInterface
     * @throws CouldNotSaveException
     */
    public function save(Data\SetInterface $set)
    {
        try {
            $this->resource->save($set);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $set;
    }

    /**
     * Load Set data by given Set Identity
     *
     * @param string $setId
     * @return Set
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($setId)
    {
        $set = $this->setFactory->create();
        $this->resource->load($set, $setId);
        if (!$set->getId()) {
            throw new NoSuchEntityException(__('Navigation Menu Set with id "%1" does not exist.', $setId));
        }
        return $set;
    }

    /**
     * Load Set data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Ewave\Navigation\Api\Data\SetSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->setCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $sets = [];
        /** @var Set $setModel */
        foreach ($collection as $setModel) {
            $setData = $this->dataSetFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $setData,
                $setModel->getData(),
                'Ewave\Navigation\Api\Data\SetInterface'
            );
            $sets[] = $this->dataObjectProcessor->buildOutputDataArray(
                $setData,
                'Ewave\Navigation\Api\Data\SetInterface'
            );
        }

        $searchResults->setItems($sets);
        return $searchResults;
    }

    /**
     * Delete Set
     *
     * @param \Ewave\Navigation\Api\Data\SetInterface $set
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\SetInterface $set)
    {
        try {
            $this->resource->delete($set);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Set by given Set Identity
     *
     * @param string $setId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($setId)
    {
        return $this->delete($this->getById($setId));
    }
}
