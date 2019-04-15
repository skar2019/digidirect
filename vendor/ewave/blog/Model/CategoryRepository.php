<?php

namespace Ewave\Blog\Model;

use Ewave\Blog\Api\CategoryRepositoryInterface;
use Ewave\Blog\Api\Data\CategoryInterface;
use Ewave\Blog\Model\Config\Provider\Status;
use Ewave\Blog\Model\ResourceModel\Category;
use Ewave\Blog\Model\ResourceModel\Category\Collection;
use Ewave\Blog\Model\ResourceModel\Post;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\Store;

/**
 * Class CategoryRepository
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @var Category
     */
    protected $resourceModel;

    /**
     * @var CategoryFactory
     */
    protected $modelFactory;

    /**
     * @var Category\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var array
     */
    protected $categoryById = [];

    /**
     * @var CurrentStoreFetcher
     */
    protected $storeFetcher;

    /**
     * CategoryRepository constructor.
     *
     * @param Category $resourceModel
     * @param CategoryFactory $modelFactory
     * @param DateTime $dateTime
     * @param Category\CollectionFactory $collectionFactory
     * @param CurrentStoreFetcher $currentStoreFetcher
     */
    public function __construct(
        Category $resourceModel,
        CategoryFactory $modelFactory,
        DateTime $dateTime,
        Category\CollectionFactory $collectionFactory,
        CurrentStoreFetcher $currentStoreFetcher
    ) {
        $this->resourceModel = $resourceModel;
        $this->modelFactory = $modelFactory;
        $this->dateTime = $dateTime;
        $this->collectionFactory = $collectionFactory;
        $this->storeFetcher = $currentStoreFetcher;
    }

    /**
     * @param int $id
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function getById($id)
    {
        if (!isset($this->categoryById[$id])) {
            $item = $this->modelFactory->create();
            $this->resourceModel->load($item, $id);
            $this->categoryById[$id] = $item;
        }
        return $this->categoryById[$id];
    }

    /**
     * @param string $ulrKey
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function getByUrlKey($ulrKey)
    {
        $storeArray = [];
        $storeId = $this->storeFetcher->getCurrentStoreId();
        $storeArray[] = $storeId;
        $categoryId = $this->resourceModel->loadCategoryIdByUrlKey($ulrKey, $storeArray);
        $category = $this->getById($categoryId);
        return $category;
    }

    /**
     * @param int $id
     * @return $this
     * @throws \Exception
     */
    public function deleteById($id)
    {
        if ($this->resourceModel->getCountPostsByCategoryId($id)) {
            throw new LocalizedException(__('The category can\'t be deleted. Please unassign Posts and try again.'));
        }
        $result = $this->resourceModel->delete($this->getById($id));
        $this->resourceModel->updateParentId($id, CategoryInterface::ROOT_CATEGORY_ID);
        return $result;
    }

    /**
     * @param \Ewave\Blog\Model\Category $item
     * @return \Ewave\Blog\Model\Category
     * @throws \Exception
     */
    public function save(\Ewave\Blog\Model\Category $item)
    {
        $item->setData(CategoryInterface::FIELD_UPDATED_AT, $this->dateTime->gmtDate());
        $this->resourceModel->save($item);
        $this->resourceModel->updateStores($item->getId(), $item->getStoreId());
        return $item;
    }

    /**
     * @param string $urlKey
     * @param int $storeId
     * @return array
     */
    public function getCategoryIdByUrlKey($urlKey, $storeId)
    {
        return $this->resourceModel->loadCategoryIdByUrlKey($urlKey, [Store::DEFAULT_STORE_ID, $storeId]);
    }

    /**
     * @param int $categoryId
     * @return string
     */
    public function getCountPostsByCategoryId($categoryId)
    {
        return $this->resourceModel->getCountPostsByCategoryId($categoryId);
    }

    /**
     * @param int $status
     * @return Category\Collection
     */
    public function getCategories($status)
    {
        /** @var Category\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(CategoryInterface::FIELD_STATUS, $status);
        return $collection;
    }

    /**
     * @param \Ewave\Blog\Model\Category $category
     * @return Category\Collection
     */
    public function getParentCategories(\Ewave\Blog\Model\Category $category)
    {
        /** @var \Ewave\Blog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->collectionFactory->create();
        $ids = [$category->getId()];
        $order = ['entity_id'];
        if ($category->getParentId()) {
            $ids = array_merge($ids, $this->prepareParentIds($category->getParentId()));
        }
        $collection->addFieldToFilter('entity_id', ['in' => $ids]);
        $collection->getSelect()->order("FIELD(" . implode(', ', array_merge($order, array_reverse($ids))) . ")");
        return $collection;
    }

    /**
     * @param int $parentId
     * @return array
     */
    protected function prepareParentIds($parentId)
    {
        $parentIds = [$parentId];
        $parent = $this->resourceModel->loadById($parentId);
        if (!empty($parent['parent_id'])) {
            $parentIds = array_merge($parentIds, $this->prepareParentIds($parent['parent_id']));
        }
        return $parentIds;
    }

    /**
     * @param int $postId
     * @param bool $activeOnly
     * @return Collection
     */
    public function getCategoriesByPostId($postId, $activeOnly = false)
    {
        /** @var \Ewave\Blog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->collectionFactory->create();
        $select = $collection->getSelect();
        $select->joinInner(
            ['post_category' => Post::CATEGORY_RELATION_TABLE],
            'main_table.entity_id = post_category.category_id'
        )->where('post_category.post_id = ?', (int)$postId);

        if ($activeOnly) {
            $collection->addFieldToFilter(CategoryInterface::FIELD_STATUS, Status::STATUS_ENABLED);
        }

        return $collection;
    }
}
