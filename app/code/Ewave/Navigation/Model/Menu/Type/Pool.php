<?php
namespace Ewave\Navigation\Model\Menu\Type;

/**
 * Class Pool
 * @package Ewave\Navigation\Model\Menu\Type
 */
class Pool
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
     * @return \Ewave\Navigation\Model\Menu\Type\AbstractType
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($className)
    {
        $menuItem = $this->objectManager->get($className);

        if (!$menuItem instanceof \Ewave\Navigation\Model\Menu\Type\AbstractType) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('%1 doesn\'t extend \Ewave\Navigation\Model\Menu\Type\AbstractType', $className)
            );
        }
        return $menuItem;
    }
}
