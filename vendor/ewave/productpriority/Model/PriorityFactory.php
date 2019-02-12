<?php
namespace Ewave\ProductPriority\Model;

/**
 * Class PriorityFactory
 * @package Ewave\ProductPriority\Model
 */
class PriorityFactory
{
    /**
     * @var \Ewave\ProductPriority\Helper\Config
     */
    protected $_priorityConfigHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_sortTypes = [];

    /**
     * PriorityFactory constructor.
     * @param \Ewave\ProductPriority\Helper\Config $config
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $sortTypes
     */
    public function __construct(
        \Ewave\ProductPriority\Helper\Config $config,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $sortTypes = []
    ) {
        $this->_priorityConfigHelper = $config;
        $this->_objectManager = $objectManager;
        $this->_sortTypes = $sortTypes;
    }

    /**
     * @param null $className
     * @param array $data
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($className = null, array $data = [])
    {
        if ($className === null) {
            $sortTypeFromSettings = $this->_priorityConfigHelper->getSortBy();
            if (isset($this->_sortTypes[$sortTypeFromSettings])) {
                $model = $this->_sortTypes[$sortTypeFromSettings];
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    new \Magento\Framework\Phrase('%1 Undefined sort type', [$sortTypeFromSettings])
                );
            }
        } else {
            $model = $this->_objectManager->create($className, $data);
        }
        if (!$model instanceof \Ewave\ProductPriority\Model\Priority\CalculateAbstract) {
            throw new \Magento\Framework\Exception\LocalizedException(
                new \Magento\Framework\Phrase('%1 doesn\'t extends \Ewave\ProductPriority\Model\Priority\CalculateAbstract', [$className])
            );
        }
        return $model;
    }
}
