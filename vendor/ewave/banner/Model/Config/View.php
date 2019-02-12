<?php

namespace Ewave\Banner\Model\Config;

use Magento\Framework\Config as ConfigView;
use Magento\Framework\View\Design\FileResolution\Fallback\ResolverInterface;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory;
use Magento\Framework\View\DesignInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class View extends ConfigView\View
{
    const MODULE_NAME = 'Ewave_Banner';

    /**
     * @var CollectionFactory
     */
    protected $themeCollectionFactory;

    /**
     * @var ResolverInterface
     */
    protected $resolver;

    /**
     * @var DesignInterface
     */
    protected $designInterface;

    /**
     * View constructor.
     *
     * @param ConfigView\FileResolverInterface $fileResolver
     * @param ConfigView\ConverterInterface $converter
     * @param ConfigView\SchemaLocatorInterface $schemaLocator
     * @param ConfigView\ValidationStateInterface $validationState
     * @param ResolverInterface $resolver
     * @param CollectionFactory $collectionFactory
     * @param DesignInterface $designInterface
     * @param string $fileName
     * @param array $idAttributes
     * @param string $domDocumentClass
     * @param string $defaultScope
     * @param array $xpath
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ConfigView\FileResolverInterface $fileResolver,
        ConfigView\ConverterInterface $converter,
        ConfigView\SchemaLocatorInterface $schemaLocator,
        ConfigView\ValidationStateInterface $validationState,
        ResolverInterface $resolver,
        CollectionFactory $collectionFactory,
        DesignInterface $designInterface,
        $fileName,
        $idAttributes = [],
        $domDocumentClass = \Magento\Framework\Config\Dom::class,
        $defaultScope = 'global',
        $xpath = []
    ) {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope,
            $xpath
        );
        $this->resolver = $resolver;
        $this->themeCollectionFactory = $collectionFactory;
        $this->designInterface = $designInterface;
    }

    /**
     * @param null $scope
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function read($scope = null)
    {
        $output = parent::read($scope);
        if ($this->designInterface->getArea() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
            $collection = $this->themeCollectionFactory->create();
            /**
             * @var $theme \Magento\Theme\Model\Theme
             */
            foreach ($collection as $theme) {
                if ($theme->isVirtual()) {
                    continue;
                }
                $designPath = $this->resolver->resolve(
                    'file',
                    'etc/view.xml',
                    $theme->getArea(),
                    $theme
                );
                if (file_exists($designPath)) {
                    try {
                        $designDom = new \DOMDocument;
                        $designDom->load($designPath);
                        $iterator = $designDom->saveXML();
                        $themeOutput = $this->_readFiles([$iterator]);
                        $output = array_replace_recursive($output, $this->processVars($themeOutput));
                    } catch (\Exception $e) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            new \Magento\Framework\Phrase('Could not read config file')
                        );
                    }
                }
            }
        }

        $output = $this->processOverride($output);
        $output = $this->processRemovals($output);
        return $output;
    }

    /**
     * @param array $vars
     * @return array
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function processVars(array $vars = [])
    {
        if (empty($vars)) {
            return $vars;
        }

        foreach ($vars as $key => $values) {
            if (!isset($values[self::MODULE_NAME])) {
                unset($vars[$key]);
            }
            foreach ($values as $moduleName => $config) {
                if ($moduleName != self::MODULE_NAME) {
                    unset($vars[$key][$moduleName]);
                    continue;
                }
            }
        }
        return $vars;
    }

    /**
     * Override keys
     *
     * @param [] $output
     * @return []
     */
    protected function processOverride($output)
    {
        foreach ($output as $key => $values) {
            if (!isset($values[self::MODULE_NAME]['override'])) {
                continue;
            }

            foreach ($values[self::MODULE_NAME]['override'] as $keyToOverride => $valuesToOverride) {
                if (isset($output[$key][self::MODULE_NAME][$keyToOverride])) {
                    $output[$key][self::MODULE_NAME][$keyToOverride] = array_replace_recursive(
                        $output[$key][self::MODULE_NAME][$keyToOverride],
                        $valuesToOverride
                    );
                } else {
                    $output[$key][self::MODULE_NAME][$keyToOverride] = $valuesToOverride;
                }
                unset($output[$key][self::MODULE_NAME]['override']);
            }
        }

        return $output;
    }

    /**
     * Unset extra keys
     *
     * @param [] $output
     * @return []
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function processRemovals($output)
    {
        foreach ($output as $key => $values) {
            if (!isset($values[self::MODULE_NAME]['remove'])) {
                continue;
            }

            foreach ($values[self::MODULE_NAME]['remove'] as $keyToOverride => $valuesToOverride) {
                if (isset($output[$key][self::MODULE_NAME][$keyToOverride])) {
                    unset($output[$key][self::MODULE_NAME][$keyToOverride]);
                }
            }
        }
        return $output;
    }
}
