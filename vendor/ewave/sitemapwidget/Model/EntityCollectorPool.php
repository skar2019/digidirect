<?php

namespace Ewave\SitemapWidget\Model;

use Ewave\SitemapWidget\Api\EntityCollectorInterface;

/**
 * Additional entity collector pool.
 */
class EntityCollectorPool
{
    /**
     * @var EntityCollectorInterface[]
     */
    protected $collectors;

    /**
     * EntityCollectorPool constructor.
     * @param array $collectors
     */
    public function __construct(
        $collectors = []
    ) {
        $this->collectors = $collectors;
    }

    /**
     * @param array $data
     * @param array $accessKeys
     */
    public function collect(array &$data, array $accessKeys)
    {
        foreach ($this->collectors as $collector) {
            if (!$collector instanceof EntityCollectorInterface) {
                throw new \InvalidArgumentException(__(
                    'Type %1 is not an instance of %2',
                    get_class($collector),
                    EntityCollectorInterface::class
                ));
            }
            $collector->collect($data, $accessKeys);
        }
    }
}
