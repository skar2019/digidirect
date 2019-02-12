<?php
namespace Ewave\Blog\Api;

use Ewave\Blog\Model\ResourceModel\Category\Collection;

/**
 * Interface CategoryRepositoryInterface
 */
interface CategoryRepositoryInterface extends AbstractRepositoryInterface
{
    /**
     * @param \Ewave\Blog\Model\Category $item
     * @return mixed
     */
    public function save(\Ewave\Blog\Model\Category $item);

    /**
     * @param string $urlKey
     * @param int $storeId
     * @return array
     */
    public function getCategoryIdByUrlKey($urlKey, $storeId);

    /**
     * @param int $status
     * @return Collection
     */
    public function getCategories($status);

    /**
     * @param int $categoryId
     * @return string
     */
    public function getCountPostsByCategoryId($categoryId);

    /**
     * @param string $ulrKey
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function getByUrlKey($ulrKey);

    /**
     * @param \Ewave\Blog\Model\Category $category
     * @return Collection
     */
    public function getParentCategories(\Ewave\Blog\Model\Category $category);

    /**
     * @param int $postId
     * @param bool $activeOnly
     * @return Collection
     */
    public function getCategoriesByPostId($postId, $activeOnly = false);
}
