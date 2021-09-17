<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\Repository;

use Amasty\BannerSlider\Api\Data\SliderInterface;
use Amasty\BannerSlider\Api\SliderRepositoryInterface;
use Amasty\BannerSlider\Model\SliderFactory;
use Amasty\BannerSlider\Model\OptionSource\Status;
use Amasty\BannerSlider\Model\ResourceModel\Slider as SliderResource;
use Amasty\BannerSlider\Model\ResourceModel\Slider\CollectionFactory;
use Amasty\BannerSlider\Model\ResourceModel\Slider\Collection;
use Amasty\Base\Model\Serializer;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Store\Model\Store;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SliderRepository implements SliderRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var SliderFactory
     */
    private $sliderFactory;

    /**
     * @var SliderResource
     */
    private $sliderResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $sliders;

    /**
     * @var CollectionFactory
     */
    private $sliderCollectionFactory;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        SliderFactory $sliderFactory,
        SliderResource $sliderResource,
        CollectionFactory $sliderCollectionFactory,
        Serializer $serializer
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->sliderFactory = $sliderFactory;
        $this->sliderResource = $sliderResource;
        $this->sliderCollectionFactory = $sliderCollectionFactory;
        $this->serializer = $serializer;
    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getAllSliders()
    {
        $collection = $this->sliderCollectionFactory->create();
        $collection->joinStoreTable();

        return $collection->getItems();
    }

    /**
     * @inheritdoc
     */
    public function save(SliderInterface $slider)
    {
        try {
            if ($slider->getId()) {
                $slider = $this->getById((int) $slider->getId())->addData($slider->getData());
            }
            $this->sliderResource->save($slider);
            unset($this->sliders[$this->getCacheKey($slider)]);
        } catch (\Exception $e) {
            if ($slider->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save slider with ID %1. Error: %2',
                        [$slider->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new slider. Error: %1', $e->getMessage()));
        }

        return $slider;
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id, int $store = 0)
    {
        $cacheKey = $this->getCacheKey([$id, $store]);
        if (!isset($this->sliders[$cacheKey])) {
            /** @var \Amasty\BannerSlider\Model\Slider $slider */
            $slider = $this->sliderFactory->create();
            if ($store !== Store::DEFAULT_STORE_ID) {
                $slider->setStoreId($store);
            }

            $this->sliderResource->load($slider, $id);
            if (!$slider->getId()) {
                throw new NoSuchEntityException(__('Slider with specified ID "%1" not found.', $id));
            }
            $this->sliders[$cacheKey] = $slider;
        }

        return $this->sliders[$cacheKey];
    }

    public function isSliderExist(int $id): bool
    {
        try {
            $this->getById($id);
            return true;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function delete(SliderInterface $slider)
    {
        try {
            $this->sliderResource->delete($slider);
            unset($this->sliders[$this->getCacheKey($slider)]);
        } catch (\Exception $e) {
            if ($slider->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove slider with ID %1. Error: %2',
                        [$slider->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove slider. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $sliderModel = $this->getById($id);
        $this->delete($sliderModel);

        return true;
    }

    /**
     * @inheritdoc
     * @throws CouldNotSaveException
     */
    public function enable(SliderInterface $slider)
    {
        $slider->setStatus((bool) Status::ENABLED);
        $this->save($slider);

        return true;
    }

    /**
     * @inheritdoc
     * @throws CouldNotSaveException
     */
    public function disable(SliderInterface $slider)
    {
        $slider->setStatus((bool) Status::DISABLED);
        $this->save($slider);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\BannerSlider\Model\ResourceModel\Slider\Collection $sliderCollection */
        $sliderCollection = $this->sliderCollectionFactory->create();

        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $sliderCollection);
        }

        $searchResults->setTotalCount($sliderCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $sliderCollection);
        }

        $sliderCollection->setCurPage($searchCriteria->getCurrentPage());
        $sliderCollection->setPageSize($searchCriteria->getPageSize());

        $sliders = [];
        /** @var SliderInterface $slider */
        foreach ($sliderCollection->getItems() as $slider) {
            $sliders[] = $this->getById($slider->getId());
        }

        $searchResults->setItems($sliders);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $sliderCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $sliderCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $sliderCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
     * Helper function that adds a SortOrder to the collection.
     *
     * @param SortOrder[] $sortOrders
     * @param Collection  $sliderCollection
     *
     * @return void
     */
    private function addOrderToCollection($sortOrders, Collection $sliderCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $sliderCollection->addOrder(
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
