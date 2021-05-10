<?php

namespace Digidirect\Faq\Model\ResourceModel;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class CategoryRepository
 *
 * @package Digidirect\Faq\Model\ResourceModel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CategoryRepository implements \Digidirect\Faq\Api\CategoryRepositoryInterface
{
    /**
     * @var \Digidirect\Faq\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Digidirect\Faq\Model\CategoryFactory
     */
    protected $categoryResource;

    /**
     * @var Category\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var UrlBuilderInterface
     */
    protected $urlBuilder;

    /**
     * CategoryRepository constructor.
     *
     * @param \Digidirect\Faq\Model\CategoryFactory $categoryFactory
     * @param Category $categoryResource
     * @param Category\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        \Digidirect\Faq\Model\CategoryFactory $categoryFactory,
        \Digidirect\Faq\Model\ResourceModel\Category $categoryResource,
        Category\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory,
        ScopeConfigInterface $scopeConfig,
        UrlInterface $urlBuilder
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryResource = $categoryResource;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->scopeConfig = $scopeConfig;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get menu Item by ID
     *
     * @param int $id
     * @return \Digidirect\Faq\Model\Category
     */
    public function getById($id)
    {
        $category = $this->categoryFactory->create();
        $this->categoryResource->load($category, $id);
        return $category;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Digidirect\Faq\Model\Category $category)
    {
        if (!$category->getId()) {
            $position = (int)$category->getOrdering();
            $findPosition = $this->categoryResource->loadByPosition($position);
            if (!empty($findPosition)) {
                $lastPosition = $this->categoryResource->loadLastPosition();
                $category->setOrdering(($lastPosition + 1));
            }
        }
        $this->categoryResource->save($category);
        return $category;
    }

    /**
     * @param int $id
     * @return $this
     * @throws \Exception
     */
    public function deleteById($id)
    {
        $stores = $this->categoryResource->lookupStoreIds($id);
        foreach ($stores as $store) {
            $questionCategoryStore = $this->scopeConfig->getValue(
                'digidirect_faq/customer_questions/category_id',
                ScopeInterface::SCOPE_STORE,
                $store
            );

            if ($id == $questionCategoryStore) {
                $message = __(
                    'Category can\'t be deleted. The category is used as customer questions storage. 
                    If you want to delete it anyway, please change the setting Stores - Configuration - Digidirect - FAQ'
                );
                throw new LocalizedException(__($message));
            }
        }
        return $this->categoryResource->delete($this->getById($id));
    }

    /**
     * Update items status
     *
     * @param [] $ids
     * @param int $status
     * @return int
     */
    public function updateStatus($ids, $status)
    {
        return $this->categoryResource->updateStatus($ids, $status);
    }

    /**
     * Retrieve faq categories depends on search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return mixed
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->getCollection();

        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->_addFilterGroupToCollection($group, $collection);
        }

        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? SortOrder::SORT_ASC : SortOrder::SORT_DESC
            );
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();

        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    /**
     * Get category collection
     *
     * @return Category\Collection
     */
    protected function getCollection()
    {
        /** @var Category\Collection $collection */
        $collection = $this->collectionFactory->create();
        return $collection;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param Category\Collection $collection
     * @return void
     */
    protected function _addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        Category\Collection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            if ($filter->getField() == 'store_id') {
                $collection->addFilterByStoreId($filter->getValue());
            } else {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }
}
