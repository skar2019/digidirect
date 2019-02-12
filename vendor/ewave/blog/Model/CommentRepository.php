<?php
namespace Ewave\Blog\Model;

use Ewave\Blog\Api\CommentRepositoryInterface;
use Ewave\Blog\Api\Data\CommentInterface;
use Ewave\Blog\Model\Comment\Source\Status;
use Ewave\Blog\Model\ResourceModel\Comment;
use Ewave\Blog\Model\ResourceModel\Comment\Collection;
use Ewave\Blog\Model\ResourceModel\Comment\CollectionFactory;

/**
 * Class CommentRepository
 */
class CommentRepository implements CommentRepositoryInterface
{
    /**
     * @var Comment
     */
    protected $resourceModel;

    /**
     * @var CommentFactory
     */
    protected $modelFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * CommentRepository constructor.
     * @param Comment $resourceModel
     * @param \Ewave\Blog\Model\CommentFactory $modelFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Comment $resourceModel,
        CommentFactory $modelFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->modelFactory = $modelFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param int $id
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function getById($id)
    {
        $item = $this->modelFactory->create();
        $this->resourceModel->load($item, $id);
        return $item;
    }

    /**
     * @param array $ids
     * @param int $status
     * @return int
     */
    public function updateStatus(array $ids, $status)
    {
        return $this->resourceModel->updateStatus($ids, $status);
    }

    /**
     * @param int $id
     * @return $this
     * @throws \Exception
     */
    public function deleteById($id)
    {
        return $this->resourceModel->delete($this->getById($id));
    }

    /**
     * @param \Ewave\Blog\Model\Comment $item
     * @return \Ewave\Blog\Model\Comment
     * @throws \Exception
     */
    public function save(\Ewave\Blog\Model\Comment $item)
    {
        $this->resourceModel->save($item);
        $item->sendAdminEmail();
        return $item;
    }

    /**
     * @param int $postId
     * @return \Magento\Framework\DB\Select
     */
    public function getCountByPostId($postId)
    {
        return $this->resourceModel->getCountByPostId($postId);
    }

    /**
     * @param int $postId
     * @param int $status
     * @return Collection
     */
    public function getCommentsList($postId, $status = Status::STATUS_APPROVED)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(CommentInterface::FIELD_POST_ID, $postId);
        $collection->addFieldToFilter(CommentInterface::FIELD_COMMENT_STATUS, $status);
        $collection->setOrder(CommentInterface::FIELD_COMMENT_DATE);
        return $collection;
    }
}
