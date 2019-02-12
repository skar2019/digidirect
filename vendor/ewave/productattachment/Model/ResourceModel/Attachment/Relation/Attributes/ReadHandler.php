<?php
namespace Ewave\ProductAttachment\Model\ResourceModel\Attachment\Relation\Attributes;

use Ewave\ProductAttachment\Model\Registry\Constants;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\App\ResourceConnection;
use Ewave\ProductAttachment\Model\ResourceModel\Attachment;
use Ewave\ProductAttachment\Model\Attachment as AttachmentModel;
use Magento\Framework\Registry;

/**
 * Class ReadHandler
 */
class ReadHandler implements ExtensionInterface
{

    /**
     * @var Attachment
     */
    protected $resourceAttachment;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * ReadHandler constructor.
     * @param Attachment $resourceAttachment
     * @param ResourceConnection $readConnection
     * @param Registry $registry
     */
    public function __construct(
        Attachment $resourceAttachment,
        ResourceConnection $readConnection,
        Registry $registry
    ) {
        $this->resourceAttachment = $resourceAttachment;
        $this->registry = $registry;
    }

    /**
     * @param object|\Ewave\ProductAttachment\Model\Attachment $entity
     * @param array $arguments
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $storeAttribute = $this->resourceAttachment->loadAttachmentAttributes(
                $entity->getId(),
                $this->registry->registry(Constants::CURRENT_STORE_ID)
            );
            foreach (AttachmentModel::getAttachmentAttributes() as $attribute) {
                if (isset($storeAttribute[$attribute])) {
                    $entity->setData($attribute, $storeAttribute[$attribute]);
                }
                if (isset($defaultAttribute[$attribute])) {
                    $entity->setData($attribute, $storeAttribute[$attribute]);
                }
            }
        }
        return $entity;
    }
}
