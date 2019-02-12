<?php
namespace Ewave\ProductCalculator\Model\ResourceModel\Calculator\Relation\Store;

use Ewave\ProductCalculator\Model\ResourceModel\Calculator;
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
     * @var Calculator
     */
    protected $resourceCalculator;

    /**
     * @param MetadataPool $metadataPool
     * @param Calculator $resourceCalculator
     */
    public function __construct(
        MetadataPool $metadataPool,
        Calculator $resourceCalculator
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceCalculator = $resourceCalculator;
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
            $stores = $this->resourceCalculator->lookupStoreIds((int)$entity->getId());
            $entity->setData('store_id', $stores);
        }
        return $entity;
    }
}
