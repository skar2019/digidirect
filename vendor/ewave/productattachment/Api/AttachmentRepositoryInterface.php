<?php
namespace Ewave\ProductAttachment\Api;

use Ewave\ProductAttachment\Model\Attachment;

/**
 * Interface AttachmentRepositoryInterface
 * @package Ewave\ProductAttachment\Api
 */
interface AttachmentRepositoryInterface
{
    /**
     * Get object by ID
     *
     * @param int $id
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function getById($id);

    /**
     * Get object by ID with store attributes
     *
     * @param int $id
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function getByIdWithAttributes($id);

    /**
     * @param Attachment $attachmentModel
     * @return mixed
     */
    public function save(Attachment $attachmentModel);

    /**
     * @param int $entityId
     * @param string $filePath
     * @return int
     */
    public function saveFilePath($entityId, $filePath);

    /**
     * @param int $id
     * @return $this
     */
    public function deleteById($id);

    /**
     * @param int $productId
     * @param int $storeId
     * @param string $attachment
     * @param string $unchecked
     * @return bool
     */
    public function updateProductRelation($productId, $storeId, string $attachment, $unchecked = '');

    /**
     * @param int $productId
     * @param int $storeId
     * @param int $status
     * @return array
     */
    public function getProductAttachments($productId, $storeId, $status);

    /**
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getRelationIds($productId, $storeId);
}
