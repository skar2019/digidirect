<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Repository;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Api\BannerRepositoryInterface;
use Amasty\BannerSlider\Model\BannerFactory;
use Amasty\BannerSlider\Model\OptionSource\Status;
use Amasty\BannerSlider\Model\ResourceModel\Banner as BannerResource;
use Amasty\BannerSlider\Model\ResourceModel\Banner\Collection as BannersCollection;
use Amasty\BannerSlider\Model\ResourceModel\Banner\CollectionFactory;
use Amasty\BannerSlider\Model\ResourceModel\Banner\Collection;
use Amasty\Base\Model\Serializer;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;

class BannerRepository implements BannerRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var BannerFactory
     */
    private $bannerFactory;

    /**
     * @var BannerResource
     */
    private $bannerResource;

    /**
     * @var CollectionFactory
     */
    private $bannerCollectionFactory;

    /**
     * Model data storage
     *
     * @var array
     */
    private $banners = [];

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        BannerFactory $bannerFactory,
        BannerResource $bannerResource,
        CollectionFactory $bannerCollectionFactory,
        Serializer $serializer,
        StoreManagerInterface $storeManager,
        Context $context
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->bannerFactory = $bannerFactory;
        $this->bannerResource = $bannerResource;
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        $this->serializer = $serializer;
        $this->context = $context;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function save(BannerInterface $banner)
    {
        try {
            if ($banner->getId()) {
                $banner = $this->getById($banner->getId(), $banner->getStoreId())->addData($banner->getData());
            }
            $this->bannerResource->save($banner);
            unset($this->banners[$this->getCacheKey($banner)]);
        } catch (\Exception $e) {
            if ($banner->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save banner with ID %1. Error: %2',
                        [$banner->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new banner. Error: %1', $e->getMessage()));
        }

        return $banner;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id, int $store = 0)
    {
        $cacheKey = $this->getCacheKey([$id, $store]);
        if (!isset($this->banners[$cacheKey])) {
            /** @var \Amasty\BannerSlider\Model\Banner $banner */
            $banner = $this->bannerFactory->create();
            if ($store !== null) {
                $banner->setStoreId($store);
            }

            $this->bannerResource->load($banner, $id);
            if (!$banner->getId()) {
                throw new NoSuchEntityException(__('Banner with specified ID "%1" not found.', $id));
            }
            $this->banners[$cacheKey] = $banner;
        }

        return $this->banners[$cacheKey];
    }

    public function getValidById($id, $store = null):  ?BannerInterface
    {
        $banner = $this->getById($id, $store);

        return $this->isBannerValid($banner) ? $banner : null;
    }

    /**
     * @inheritdoc
     */
    public function delete(BannerInterface $banner)
    {
        try {
            $this->bannerResource->delete($banner);
            unset($this->banners[$this->getCacheKey($banner)]);
        } catch (\Exception $e) {
            if ($banner->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove banner with ID %1. Error: %2',
                        [$banner->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove banner. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $bannerModel = $this->getById($id);
        $this->delete($bannerModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function enable(BannerInterface $banner)
    {
        $banner->setStatus((bool) Status::ENABLED);
        $this->bannerResource->save($banner);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function disable(BannerInterface $banner)
    {
        $banner->setStatus((bool) Status::DISABLED);
        $this->bannerResource->save($banner);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var BannersCollection $bannerCollection */
        $bannerCollection = $this->bannerCollectionFactory->create();
        $this->applyScheduleCondition($bannerCollection);

        return $this->getSearchResults($bannerCollection, $searchCriteria);
    }

    private function applyScheduleCondition(BannersCollection $bannerCollection): void
    {
        $bannerCollection->getSelect()->where(
            '(ISNULL(main_table.' . BannerInterface::START_DATE . ') OR main_table.'
            . BannerInterface::START_DATE . ' <= CURDATE()) AND '
            . '(ISNULL(main_table.' . BannerInterface::END_DATE
            . ') OR main_table.' . BannerInterface::END_DATE . ' >= CURDATE())'
        );
    }

    private function getSearchResults(
        Collection $bannerCollection,
        SearchCriteriaInterface $searchCriteria
    ): SearchResultsInterface {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $this->applySearchCriteriaToCollection($bannerCollection, $searchCriteria);
        $searchResults->setTotalCount($bannerCollection->getSize());
        $banners = [];
        $storeId = (int)$this->storeManager->getStore()->getId();

        /** @var BannerInterface $banner */
        foreach ($bannerCollection->getItems() as $banner) {
            $banners[] = $this->getById((int)$banner->getId(), $storeId);
        }

        $searchResults->setItems($banners);

        return $searchResults;
    }

    protected function applySearchCriteriaToCollection(
        Collection $bannerCollection,
        SearchCriteriaInterface $searchCriteria
    ): void {
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $bannerCollection);
        }

        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $bannerCollection);
        }

        $bannerCollection->setCurPage($searchCriteria->getCurrentPage());
        $bannerCollection->setPageSize($searchCriteria->getPageSize());
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param int[]|string $bannersIds
     * @return SearchResultsInterface
     */
    public function getOrderedBannerList(
        SearchCriteriaInterface $searchCriteria,
        $bannersIds
    ): SearchResultsInterface {
        /** @var BannersCollection $bannerCollection */
        $bannerCollection = $this->bannerCollectionFactory->create();
        $bannerCollection->addOrderByIds($bannersIds);
        $this->applyScheduleCondition($bannerCollection);

        return $this->getSearchResults($bannerCollection, $searchCriteria);
    }

    public function getValidList(SearchCriteriaInterface $searchCriteria): array
    {
        $bannersList = $this->getList($searchCriteria);

        return $this->validateBanners($bannersList);
    }

    public function validateBanners(SearchResultsInterface $bannersList): array
    {
        foreach ($bannersList->getItems() as $banner) {
            if ($this->isBannerValid($banner)) {
                $banners[] = $banner;
            }
        }

        return $banners ?? [];
    }

    public function isBannerValid(BannerInterface $banner): bool
    {
        $currentCustomerGroup = $this->context->getValue(BannerInterface::CUSTOMER_GROUP);
        $customerGroups = $banner->getCustomerGroup();
        if (is_string($customerGroups)) {
            $customerGroups = explode(',', $customerGroups);
        }

        return $banner->getStatus() && in_array($currentCustomerGroup, $customerGroups);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $bannerCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $bannerCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $bannerCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $bannerCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $bannerCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $bannerCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }

    /**
     * Get key for cache
     *
     * @param \Magento\Framework\Model\AbstractModel|array $data
     * @return string
     */
    protected function getCacheKey($data)
    {
        if (is_object($data)) {
            $data = [(int) $data->getId(), (int) $data->getStoreId()];
        }

        return sha1($this->serializer->serialize($data));
    }
}
