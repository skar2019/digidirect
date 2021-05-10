<?php
namespace Digidirect\Faq\Model\ResourceModel\Faq\Relation\Tag;

use Digidirect\Faq\Model\ResourceModel\Faq;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Faq
     */
    protected $resourceFaq;
    
    /**
     * @param MetadataPool $metadataPool
     * @param Faq $resourceFaq
     */
    public function __construct(
        MetadataPool $metadataPool,
        Faq $resourceFaq
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceFaq = $resourceFaq;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $tagIds = $this->resourceFaq->lookupTagIds((int)$entity->getId());
            $entity->setData('tag_id', $tagIds);
        }
        return $entity;
    }
}
