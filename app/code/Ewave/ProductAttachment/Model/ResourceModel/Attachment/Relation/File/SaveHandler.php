<?php
namespace Ewave\ProductAttachment\Model\ResourceModel\Attachment\Relation\File;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Ewave\ProductAttachment\Model\ResourceModel\Attachment;

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
        $entity->saveAttachmentFile();
        return $entity;
    }
}
