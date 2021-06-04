<?php

namespace Ewave\MagentoFixes\Preference\Magento\Framework\View\Element;

use Magento\Framework\Config\DataInterface;
use Magento\Framework\Config\DataInterfaceFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Data\Argument\InterpreterInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponent\Config\ManagerInterface;
use Magento\Framework\View\Element\UiComponent\ContextFactory;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Framework\View\Element\UiComponent\Factory\ComponentFactoryInterface;
use Magento\Framework\Authorization;

class UiComponentFactory extends \Magento\Framework\View\Element\UiComponentFactory
{
    /**
     * @var Authorization
     */
    protected $authorization;

    /**
     * @var array
     */
    protected $checkAclResources = [];

    /**
     * UiComponentFactory constructor.
     * @param Authorization $authorization
     * @param ObjectManagerInterface $objectManager
     * @param ManagerInterface $componentManager
     * @param InterpreterInterface $argumentInterpreter
     * @param ContextFactory $contextFactory
     * @param array $data
     * @param array $componentChildFactories
     * @param DataInterface|null $definitionData
     * @param DataInterfaceFactory|null $configFactory
     * @param array $checkAclResources
     */
    public function __construct(
        Authorization $authorization,
        ObjectManagerInterface $objectManager,
        ManagerInterface $componentManager,
        InterpreterInterface $argumentInterpreter,
        ContextFactory $contextFactory,
        array $data = [],
        array $componentChildFactories = [],
        DataInterface $definitionData = null,
        DataInterfaceFactory $configFactory = null,
        $checkAclResources = []
    ) {

        parent::__construct(
            $objectManager,
            $componentManager,
            $argumentInterpreter,
            $contextFactory,
            $data,
            $componentChildFactories,
            $definitionData,
            $configFactory
        );

        $this->authorization = $authorization;
        $this->checkAclResources = $checkAclResources;
    }

    /**
     * Create child components
     *
     * @param array $bundleComponents
     * @param ContextInterface $renderContext
     * @param string $identifier
     * @param array $arguments
     * @return UiComponentInterface
     */
    protected function createChildComponent(
        array &$bundleComponents,
        ContextInterface $renderContext,
        $identifier,
        array $arguments = []
    ) {
        if (array_key_exists($identifier, $this->checkAclResources)
            && !$this->authorization->isAllowed($this->checkAclResources[$identifier])) {
            return null;
        }
        return parent::createChildComponent($bundleComponents, $renderContext, $identifier, $arguments);
    }
}
