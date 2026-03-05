<?php

namespace Digidirect\AbstractGiftCard\Model\Service;

use Magento\Framework\Api\ObjectFactory;

/**
 * Class Factory
 */
class Factory
{
    /**
     * Object manager
     *
     * @var ObjectFactory
     */
    protected $objectFactory;

    /**
     * Construct
     *
     * @param ObjectFactory $objectFactory
     */
    public function __construct(ObjectFactory $objectFactory)
    {
        $this->objectFactory = $objectFactory;
    }

    /**
     * Creates new instances of service models
     *
     * @param string $className
     * @param array $data
     * @return \Digidirect\AbstractGiftCard\Model\ServiceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($className, $data = [])
    {
        $method = $this->objectFactory->create($className, $data);
        if (!$method instanceof \Digidirect\AbstractGiftCard\Model\ServiceInterface) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('%1 class doesn\'t implement \Digidirect\AbstractGiftCard\Model\ServiceInterface', $className)
            );
        }
        return $method;
    }
}
