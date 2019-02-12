<?php
namespace Ewave\ProductAttachment\Model\ResourceModel\Attachment\Relation\Attributes;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Ewave\ProductAttachment\Model\ResourceModel\Attachment;
use Ewave\ProductAttachment\Model\Attachment as AttachmentModel;

/**
 * Class SaveHandler
 * @package Ewave\ProductAttachment\Model\ResourceModel\Attachment\Relation\Attributes
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var Attachment
     */
    protected $resourceAttachment;

    /**
     * SaveHandler constructor.
     * @param Attachment $resourceAttachment
     * @param ResourceConnection $readConnection
     */
    public function __construct(
        Attachment $resourceAttachment,
        ResourceConnection $readConnection
    ) {
        $this->resourceAttachment = $resourceAttachment;
    }

    /**
     * @param \Ewave\ProductAttachment\Model\Attachment $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $attributes = $this->resourceAttachment->loadAttachmentAttributes($entity->getId(), $entity->getStoreId());
        if (empty($attributes)) {
            $attributes = [
                'attachment_id' => $entity->getId(),
                'store_id'  => $entity->getStoreId()
            ];
        }
        foreach (AttachmentModel::getAttachmentAttributes() as $attribute) {
            $attributes[$attribute] = $entity->getData($attribute);
        }
        $this->resourceAttachment->updateAttachmentAttributes($attributes);
        return $entity;
    }
}
