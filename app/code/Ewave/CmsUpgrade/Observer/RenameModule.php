<?php
namespace Ewave\CmsUpgrade\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class RenameModule
 *
 * @package Ewave\CmsUpgrade\Observer
 */
class RenameModule implements ObserverInterface
{
    /**
     * @var array
     */
    protected $enabledModulesArray;

    /**
     * RenameModule constructor.
     *
     * @param array $enabledModulesArray
     */
    public function __construct(array $enabledModulesArray = [])
    {
        $this->enabledModulesArray = $enabledModulesArray;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if (isset($this->enabledModulesArray[$block->getModuleName()])) {
            $parentModule = $this->enabledModulesArray[$block->getModuleName()]['parent_name'] ?? null;
            if ($parentModule) {
                $block->setModuleName($parentModule);
            }
        }
    }
}
