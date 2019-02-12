<?php
namespace Ewave\ProductAttachment\Model\ResourceModel\Attachment\Relation\File;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Ewave\ProductAttachment\Model\ResourceModel\Attachment;

/**
 * Class ReadHandler
 * @package Ewave\ProductAttachment\Model\ResourceModel\Attachment\Relation\File
 */
class ReadHandler implements ExtensionInterface
{
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
        if ($entity->isExists() && $entity->getFilePath()) {
            $entity->setFile(
                [
                    [
                        'exists' => true,
                        'file' => $entity->getFilePath(),
                        'url' => $entity->getWebUrl(),
                        'size' => $entity->getFileSize()
                    ]
                ]
            );
        }
        return $entity;
    }
}
