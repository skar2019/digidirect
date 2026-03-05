<?php

namespace Digidirect\Collect\Model;

use Digidirect\Collect\Api\CollectPlaceRepositoryInterface;
use Magento\Framework\Api\ObjectFactory;

class StorageFactory
{
    /**
     * ObjectFactory
     *
     * @var ObjectFactory
     */
    protected $objectFactory;

    /**
     * In-memory storage instances cache for get()
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Storage Factory construct
     *
     * @param ObjectFactory $objectFactory
     */
    public function __construct(ObjectFactory $objectFactory)
    {
        $this->objectFactory = $objectFactory;
    }

    /**
     * Get observer model instance
     *
     * @param string $className
     * @return mixed
     */
    public function get($className)
    {
        $className = ltrim($className, '\\');

        if (!isset($this->instances[$className])) {
            $this->instances[$className] = $this->objectFactory->create($className);
        }

        return $this->instances[$className];
    }

    /**
     * Create collect place repository model instance (CollectPlaceRepositoryInterface)
     *
     * @param string $className
     * @param array $arguments
     * @throws \Exception
     * @return CollectPlaceRepositoryInterface
     */
    public function create($className, array $arguments = [])
    {
        $object = $this->objectFactory->create(ltrim($className, '\\'), $arguments);
        if ($object instanceof CollectPlaceRepositoryInterface) {
            return $object;
        }
        throw new \Exception('Collect place storage must implement Digidirect\Collect\Api\CollectPlaceRepositoryInterface');
    }
}
