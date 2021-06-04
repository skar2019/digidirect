<?php
namespace Ewave\Blog\Api;

use Ewave\Blog\Model\ResourceModel\Tag\Collection;

/**
 * Interface TagRepositoryInterface
 */
interface TagRepositoryInterface
{
    /**
     * @param int $storeId
     * @return Collection
     */
    public function getRandomTags($storeId);

    /**
     * @param string $tagName
     * @return \Ewave\Blog\Model\Tag
     */
    public function getByName($tagName);

    /**
     * @param int $postId
     * @return Collection
     */
    public function getTagsByPostId($postId);
}
