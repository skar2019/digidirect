<?php
namespace Ewave\ProductAttachment\Model;

use Ewave\ProductAttachment\Api\AttachmentRepositoryInterface;
use Ewave\ProductAttachment\Model\Registry\Constants;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Ewave\ProductAttachment\Model\Attachment as AttachmentModel;

/**
 * Class AttachmentRepository
 * @package Ewave\ProductAttachment\Model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AttachmentRepository implements AttachmentRepositoryInterface
{
    /**
     * @var AttachmentFactory
     */
    protected $attachmentFactory;

    /**
     * @var ResourceModel\Attachment
     */
    protected $attachmentResource;

    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $jsonDecoder;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var ResourceModel\Attachment\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * AttachmentRepository constructor.
     * @param AttachmentFactory $attachmentFactory
     * @param ResourceModel\Attachment $attachmentResource
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Attachment\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Ewave\ProductAttachment\Model\AttachmentFactory $attachmentFactory,
        \Ewave\ProductAttachment\Model\ResourceModel\Attachment $attachmentResource,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Framework\Registry $registry,
        \Ewave\ProductAttachment\Model\ResourceModel\Attachment\CollectionFactory $collectionFactory
    ) {
        $this->attachmentFactory = $attachmentFactory;
        $this->attachmentResource = $attachmentResource;
        $this->jsonDecoder = $jsonDecoder;
        $this->registry = $registry;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param Attachment $attachmentModel
     * @return Attachment
     * @throws CouldNotSaveException
     */
    public function save(Attachment $attachmentModel)
    {
        try {
            $this->attachmentResource->save($attachmentModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $attachmentModel;
    }

    /**
     * @param int $id
     * @return Attachment
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $attachment = $this->attachmentFactory->create();
        $this->attachmentResource->load($attachment, $id);
        if (!$attachment->getId()) {
            throw new NoSuchEntityException(__('Attachment with id "%1" does not exist.', $id));
        }
        return $attachment;
    }

    /**
     * @param int $id
     * @return Attachment
     * @throws NoSuchEntityException
     */
    public function getByIdWithAttributes($id)
    {
        $attachment = $this->getById($id);
        $storeAttribute = $this->attachmentResource->loadAttachmentAttributes(
            $attachment->getId(),
            $this->registry->registry(Constants::CURRENT_STORE_ID)
        );
        $defaultAttribute = $this->attachmentResource->loadAttachmentAttributes(
            $attachment->getId(),
            $this->registry->registry(AttachmentModel::ALL_STORE_ID)
        );
        foreach (AttachmentModel::getAttachmentAttributes() as $attribute) {
            if (isset($storeAttribute[$attribute])) {
                $attachment->setData($attribute, $storeAttribute[$attribute]);
            } elseif (isset($defaultAttribute[$attribute])) {
                $attachment->setData($attribute, $defaultAttribute[$attribute]);
            }
        }
        return $attachment;
    }

    /**
     * @param int $id
     * @return $this
     * @throws \Exception
     */
    public function deleteById($id)
    {
        $attachment = $this->getById($id);
        if (!$attachment->getId()) {
            throw new NoSuchEntityException(__('Attachment with id "%1" does not exist.', $id));
        }
        $this->attachmentResource->delete($this->getById($id));
        $attachment->removeFile();
        return $this;
    }

    /**
     * @param int $entityId
     * @param string $filePath
     * @return int
     */
    public function saveFilePath($entityId, $filePath)
    {
        return $this->attachmentResource->updateFilePath($entityId, $filePath);
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @param string $attachments
     * @param string $unchecked
     * @return bool
     */
    public function updateProductRelation($productId, $storeId, string $attachments, $unchecked = '')
    {
        $attachmentData = [];
        $uncheckedData = [];
        if (!empty($attachments)) {
            $attachmentData = $this->jsonDecoder->decode($attachments);
        }
        if (!empty($unchecked)) {
            $uncheckedData = $this->jsonDecoder->decode($unchecked);
        }
        return $this->attachmentResource->saveProductRelation($productId, $storeId, $attachmentData, $uncheckedData);
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getProductAttachmentsPosition($productId, $storeId)
    {
        return $this->attachmentResource->loadProductAttachments($productId, $storeId);
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getRelationIds($productId, $storeId)
    {
        $relatedIds = [];
        $storeAttached = $this->attachmentResource->loadAllProductAttachmentsByStore($productId, $storeId);
        $defaultAttached = $this->attachmentResource->loadRelationData(
            $productId,
            AttachmentModel::ALL_STORE_ID,
            AttachmentModel::ATTACHMENT_ID
        );
        foreach ($storeAttached as $item) {
            if (!empty($item[AttachmentModel::ATTACHED])) {
                $relatedIds[] = $item[AttachmentModel::ATTACHMENT_ID];
            } elseif (in_array($item[AttachmentModel::ATTACHMENT_ID], $defaultAttached)) {
                unset($defaultAttached[array_search($item[AttachmentModel::ATTACHMENT_ID], $defaultAttached)]);
            }
        }
        return array_merge($relatedIds, $defaultAttached);
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @param int $status
     * @return array
     */
    public function getProductAttachments($productId, $storeId, $status)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(AttachmentModel::STATUS, $status);
        $collection->joinAttributes($storeId);
        $collection->addFilterByProductId(
            $productId,
            $storeId
        );
        $collection->groupByEntityId();
        return $collection;
    }
}
