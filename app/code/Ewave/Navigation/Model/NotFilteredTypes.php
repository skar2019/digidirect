<?php

namespace Ewave\Navigation\Model;

use Ewave\Navigation\Model\ResourceModel\Type\CollectionFactory;
use Ewave\Navigation\Model\ResourceModel\Type\Collection;

/**
 * Instead of using factory everywhere use this class if you need all types to be selected
 * @since 1.3.0
 */
class NotFilteredTypes
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var null|Collection
     */
    protected $collection = null;

    /**
     * NotFilteredTypes constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        if (null === $this->collection) {
            $this->collection = $this->collectionFactory->create();
        }

        return $this->collection;
    }
}
