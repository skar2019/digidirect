<?php
namespace Digidirect\Faq\Model\ResourceModel\Category\Relation\Store;

use Digidirect\Faq\Model\ResourceModel\Category;
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
     * @var Category
     */
    protected $resourceCategory;

    /**
     * @param MetadataPool $metadataPool
     * @param Category $resourceCategory
     */
    public function __construct(
        MetadataPool $metadataPool,
        Category $resourceCategory
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceCategory = $resourceCategory;
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
            $stores = $this->resourceCategory->lookupStoreIds((int)$entity->getId());
            $entity->setData('store_id', $stores);
        }
        return $entity;
    }
}
