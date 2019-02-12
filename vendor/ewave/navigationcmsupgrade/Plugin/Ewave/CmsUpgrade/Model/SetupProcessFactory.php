<?php
namespace Ewave\NavigationCMSUpgrade\Plugin\Ewave\CmsUpgrade\Model;

use Ewave\CmsUpgrade\Model\SetupProcessorFactory as CmsUpgradeSetupFactory;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class SetupProcessFactory
 *
 * @package Ewave\NavigationCMSUpgrade\Plugin\Ewave\CmsUpgrade\Model
 */
class SetupProcessFactory
{
    /**
     * @var array
     */
    protected $customProcessorsConfig;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * SetupProcessFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param array $config
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $config = []
    ) {
        $this->customProcessorsConfig = $config;
        $this->objectManager = $objectManager;
    }

    /**
     * @param CmsUpgradeSetupFactory $setupProcessorFactory
     * @param \Closure $function
     * @param string $type
     * @return object
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundMakeProcessor(CmsUpgradeSetupFactory $setupProcessorFactory, \Closure $function, $type)
    {
        if (isset($this->customProcessorsConfig[$type])) {
            return $this->objectManager->get($this->customProcessorsConfig[$type]);
        }

        return $function($type);
    }
}
