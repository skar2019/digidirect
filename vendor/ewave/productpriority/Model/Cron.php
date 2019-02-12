<?php
namespace Ewave\ProductPriority\Model;

/**
 * Class Priority
 * @package Ewave\ProductPriority\Model
 */
class Cron
{
    /**
     * @var PriorityFactory
     */
    protected $_priorityFactory;

    /**
     * @var \Ewave\ProductPriority\Helper\Config
     */
    protected $_configHelper;

    /**
     * Cron constructor.
     * @param PriorityFactory $priorityFactory
     * @param \Ewave\ProductPriority\Helper\Config $configHelper
     */
    public function __construct(
        \Ewave\ProductPriority\Model\PriorityFactory $priorityFactory,
        \Ewave\ProductPriority\Helper\Config $configHelper
    ) {
        $this->_priorityFactory = $priorityFactory;
        $this->_configHelper = $configHelper;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        if ($this->_configHelper->isEnabled()) {
            $sortInstance = $this->_priorityFactory->create();
            $sortInstance->calculate();
        }
        return $this;
    }
}
