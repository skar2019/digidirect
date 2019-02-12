<?php
namespace Ewave\AbstractEntity\Model\ResourceModel\AbstractEntity\Relation\Indexer;

use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Ewave\AbstractEntity\Model\Indexer\AbstractEntity as AEIndexer;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;

/**
 * Class IndexHandler
 * @package Ewave\AbstractEntity\Model\ResourceModel\AbstractEntity\Relation\Indexer
 */
class IndexHandler implements ExtensionInterface
{

    /**
     * @var AEIndexer
     */
    protected $indexerProcessor;

    /**
     * @var CacheTypeListInterface
     */
    protected $cache;

    /**
     * AbstractHandler constructor.
     * @param AEIndexer $indexerProcessor
     * @param CacheTypeListInterface $cache
     */
    public function __construct(
        AEIndexer $indexerProcessor,
        CacheTypeListInterface $cache
    ) {
        $this->indexerProcessor = $indexerProcessor;
        $this->cache = $cache;
    }

    /**
     * @param AbstractEntityInterface $entity
     * @param array $arguments
     * @return object
     */
    public function execute($entity, $arguments = [])
    {
        $this->indexerProcessor->reindexRow($entity, $arguments);
        $this->cache->invalidate('full_page');
        return $entity;
    }
}
