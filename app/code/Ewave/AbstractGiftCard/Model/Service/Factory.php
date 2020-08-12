<?php

namespace Ewave\AbstractGiftCard\Model\Service;

/**
 * Class Factory
 */
class Factory
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Creates new instances of service models
     *
     * @param string $className
     * @param array $data
     * @return \Ewave\AbstractGiftCard\Model\ServiceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($className, $data = [])
    {
        $method = $this->_objectManager->create($className, $data);
        if (!$method instanceof \Ewave\AbstractGiftCard\Model\ServiceInterface) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('%1 class doesn\'t implement \Ewave\AbstractGiftCard\Model\ServiceInterface', $className)
            );
        }
        return $method;
    }
}
