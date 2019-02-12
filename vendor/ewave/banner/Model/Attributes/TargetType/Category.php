<?php

namespace Ewave\Banner\Model\Attributes\TargetType;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Banner\Model\Banner as BannerModel;

class Category implements TargetTypeInterface
{
    const TARGET_TYPE_REQUEST_CODE = 'category_id';

    /**
     * @var CategoryCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var array
     */
    protected $categoryUrlById = [];

    /**
     * Category constructor.
     *
     * @param CategoryCollectionFactory $collectionFactory
     */
    public function __construct(CategoryCollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param BannerModel $banner
     * @return mixed
     */
    public function getBackendAttributeForSave(BannerModel $banner)
    {
        return $banner->getData(self::TARGET_TYPE_REQUEST_CODE);
    }

    /**
     * @param int|string $categoryId
     * @return null|string
     */
    public function getUrl($categoryId)
    {
        $url = null;
        $categoryUrl = isset($this->categoryUrlById[$categoryId]) ? $this->categoryUrlById[$categoryId] : null;
        if ($categoryId && !$categoryUrl) {
            $category = $this->getCategoryById($categoryId);
            $url = $category ? $category->getUrl() : null;
        } else {
            $url = $categoryUrl;
        }
        return $url;
    }

    /**
     * @param int $entityId
     * @return string|null
     */
    public function getValue($entityId)
    {
        $value = null;
        if ($entityId) {
            $category = $this->getCategoryById($entityId);
            $value = $category ? $category->getId() : null;
        }
        return $value;
    }

    /**
     * @param BannerModel $banner
     * @param null $currentAttribute
     * @return mixed|null
     */
    public function getBackendAttributeForEdit(BannerModel $banner, $currentAttribute = null)
    {
        if ($currentAttribute && $currentAttribute == self::TARGET_TYPE_REQUEST_CODE) {
            $targetId = $banner->getData('target_id');
            $banner->setData(self::TARGET_TYPE_REQUEST_CODE, $targetId);
            return $targetId;
        }
        return null;
    }

    /**
     * @param int $categoryId
     * @return CategoryInterface|\Magento\Catalog\Model\Category
     */
    protected function getCategoryById($categoryId)
    {
        if (!isset($this->categoryUrlById[$categoryId])) {
            /**
             * @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection
             */
            $collection = $this->collectionFactory->create();

            /**
             * @var $category \Magento\Catalog\Model\Category
             */
            $category = $collection->addFieldToFilter('is_active', ['eq' => true])
                ->addIdFilter([$categoryId])
                ->setPageSize(1)
                ->addUrlRewriteToResult()
                ->getFirstItem();
            $this->categoryUrlById[$categoryId] = $category;
        }

        return $this->categoryUrlById[$categoryId];
    }
}
