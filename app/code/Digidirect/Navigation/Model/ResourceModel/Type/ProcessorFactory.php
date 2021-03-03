<?php
namespace Digidirect\Navigation\Model\ResourceModel\Type;

use Digidirect\Navigation\Model\ResourceModel\Type\SaveProcessorInterface as MenuItemTypeSaveProcessor;

/**
 * Class ProcessorsFactory
 * @package Digidirect\Navigation\Model\ResourceModel\Type
 */
class ProcessorFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Pool constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $className
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get(string $className)
    {
        $processor = $this->objectManager->get($className);

        if (!($processor instanceof MenuItemTypeSaveProcessor)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('%1 must implement %2', $className, MenuItemTypeSaveProcessor::class)
            );
        }
        return $processor;
    }
}
