<?php

namespace Ewave\Blog\Model;

use Ewave\Blog\Api\Data\TagInterface;
use Ewave\Blog\Api\TagRepositoryInterface;
use Ewave\Blog\Model\ResourceModel\Tag;
use Ewave\Blog\Model\TagFactory;
use Ewave\Blog\Model\ResourceModel\Tag\CollectionFactory;
use Magento\Store\Model\Store;

class TagRepository implements TagRepositoryInterface
{
    /**
     * @var Post
     */
    protected $resourceModel;

    /**
     * @var TagFactory
     */
    protected $modelFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Ewave\Blog\Model\Tag[]
     */
    protected $tagByName = [];

    /**
     * @var array
     */
    protected $tagsByPostId = [];

    /**
     * TagRepository constructor.
     *
     * @param Tag $resourceModel
     * @param \Ewave\Blog\Model\TagFactory $modelFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Tag $resourceModel,
        TagFactory $modelFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->modelFactory = $modelFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param string $tagName
     * @return \Ewave\Blog\Model\Tag
     */
    public function getByName($tagName)
    {
        if (!isset($this->tagByName[$tagName])) {
            $item = $this->modelFactory->create();
            $this->resourceModel->load($item, $tagName, TagInterface::FILED_NAME);
            $this->tagByName[$tagName] = $item;
        }
        return $this->tagByName[$tagName];
    }

    /**
     * @param int $storeId
     * @return Tag\Collection
     */
    public function getRandomTags($storeId)
    {
        $stores = [Store::DEFAULT_STORE_ID, $storeId];
        /** @var Tag\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->getSelect()
            ->joinInner(
                ['tag_rel' => $this->resourceModel->getTable('ewave_blog_post_tags')],
                'main_table.entity_id = tag_rel.tag_id',
                []
            )
            ->joinInner(
                ['post_cat_rel' => $this->resourceModel->getTable('ewave_blog_post_categories')],
                'post_cat_rel.post_id = tag_rel.post_id',
                []
            )
            ->joinInner(
                ['cat_store_rel' => $this->resourceModel->getTable('ewave_blog_category_stores')],
                'cat_store_rel.category_id = post_cat_rel.category_id',
                []
            )
            ->where('cat_store_rel.store_id IN (?)', $stores)
            ->group('main_table.entity_id')
            ->order((new \Zend_Db_Expr('RAND()')));
        return $collection;
    }

    /**
     * @param int $postId
     * @return Tag\Collection
     */
    public function getTagsByPostId($postId)
    {
        if (!isset($this->tagsByPostId[$postId])) {
            $collection = $this->collectionFactory->create();
            $collection->getSelect()->joinInner(
                ['rel' => Tag::TAG_POST_RELATION_TABLE],
                'main_table.entity_id = rel.tag_id'
            )->where('rel.post_id = ?', (int)$postId);
            $this->tagsByPostId[$postId] = $collection;
        }
        return $this->tagsByPostId[$postId];
    }
}
