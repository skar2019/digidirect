<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark\Base
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */

namespace MageSpark\Base\Observer;

use MageSpark\Base\Helper\Module;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\Asset\Repository;
use Magento\Config\Model\Config\Structure;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\FileSystemException;

/**
 * Class GenerateInformationTab
 *
 * @package MageSpark\Base\Observer
 */
class GenerateInformationTab implements ObserverInterface
{
    const SEO_PARAMS = '?utm_source=extension&utm_medium=backend&utm_campaign=';

    const MAGENTO_VERSION = '_m2';

    /**
     * @var array
     */
    private $moduleData = null;
    /**
     * @var Block
     */
    private $block;

    /**
     * @var Module
     */
    private $moduleHelper;

    /**
     * @var string
     */
    private $moduleLink;

    /**
     * @var string
     */
    private $moduleCode;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * @var Structure
     */
    private $configStructure;

    /**
     * GenerateInformationTab constructor.
     *
     * @param Module $moduleHelper
     * @param Manager $moduleManager
     * @param Repository $assetRepo
     * @param Structure $configStructure
     */
    public function __construct(
        Module $moduleHelper,
        Manager $moduleManager,
        Repository $assetRepo,
        Structure $configStructure
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->moduleManager = $moduleManager;
        $this->assetRepo = $assetRepo;
        $this->configStructure = $configStructure;
    }

    /**
     * Initialize an obeserver
     *
     * @param Observer $observer
     * @throws FileSystemException
     */
    public function execute(Observer $observer)
    {
        $block = $observer->getBlock();
        if ($block) {
            $this->setBlock($block);
            $html = $this->generateHtml();
            $block->setContent($html);
        }
    }

    /**
     * Generating user guide with version information
     *
     * @return string
     * @throws FileSystemException
     */
    private function generateHtml()
    {
        $html = '<div class="magespark-info-block">'
            . $this->showVersionInfo()
            . $this->showUserGuideLink()
            . $this->additionalContent()
            . $this->showModuleExistingConflicts();
        $html .= '</div>';

        return $html;
    }

    /**
     * Get site url with logo
     *
     * @return string
     */
    private function getLogoHtml()
    {
        $src = $this->assetRepo->getUrl("MageSpark_Base::images/magespark_logo.svg");
        $href = 'https://www.magespark.com' . $this->getSeoparams() . 'magespark_logo_' . $this->getModuleCode();
        $html = '<a target="_blank" href="' . $href . '"><img class="magespark-logo" src="' . $src . '"/></a>';

        return $html;
    }

    /**
     * Get additional content
     *
     * @return string
     */
    private function additionalContent()
    {
        $html = '';
        $content = $this->getBlock()->getAdditionalModuleContent();
        if ($content) {
            if (!is_array($content)) {
                $content = [
                    [
                        'type' => 'success',
                        'text' => $content
                    ]
                ];
            }

            foreach ($content as $message) {
                if (isset($message['type']) && isset($message['text'])) {
                    $html .= '<div class="magespark-additional-content"><span class="message ' . $message['type'] . '">'
                        . $message['text']
                        . '</span></div>';
                }
            }
        }

        return $html;
    }

    /**
     * Display version information
     *
     * @return string
     * @throws FileSystemException
     */
    private function showVersionInfo()
    {
        $html = '<div class="magespark-module-version">';

        $currentVer = $this->getCurrentVersion();
        if ($currentVer) {
            $isVersionLast = $this->isLastVersion($currentVer);
            $class = $isVersionLast ? 'last-version' : '';
            $html .= '<div><span class="version-title">'
                . $this->getModuleName() . ' '
                . '<span class="module-version ' . $class . '">' . $currentVer . '</span>'
                . __(' by ')
                . '</span>'
                . $this->getLogoHtml()
                . '</div>';

            if (!$isVersionLast) {
                $html .=
                    '<div><span class="upgrade-error message message-warning">'
                    . __(
                        'Update is available and recommended. See the '
                        . '<a target="_blank" href="%1">Change Log</a>',
                        $this->getChangeLogLink()
                    )
                    . '</span></div>';
            }
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Get current module version
     *
     * @return null
     * @throws FileSystemException
     */
    private function getCurrentVersion()
    {
        $data = $this->moduleHelper->getModuleInfo($this->getModuleCode());

        return isset($data['version']) ? $data['version'] : null;
    }

    /**
     * Get code of the module
     *
     * @return string
     */
    private function getModuleCode()
    {
        if (!$this->moduleCode) {
            $this->moduleCode = '';
            $class = get_class($this->getBlock());
            if ($class) {
                $class = explode('\\', $class);
                if (isset($class[0]) && isset($class[1])) {
                    $this->moduleCode = $class[0] . '_' . $class[1];
                }
            }
        }

        return $this->moduleCode;
    }

    /**
     * Change log link with module code
     *
     * @return string
     */
    private function getChangeLogLink()
    {
        return $this->getModuleLink()
            . $this->getSeoparams() . 'changelog_' . $this->getModuleCode() . '#changelog';
    }

    /**
     * Display user link with user guide
     *
     * @return string
     */
    private function showUserGuideLink()
    {
        $html = '<div class="magespark-user-guide"><span class="message success">'
            . __(
                'Need help with the settings?'
                . '  Please  consult the <a target="_blank" href="%1">user guide</a>'
                . ' to configure the extension properly.',
                $this->getUserGuideLink()
            )
            . '</span></div>';

        return $html;
    }

    /**
     * Show user guide with module code and seo link
     *
     * @return string
     */
    private function getUserGuideLink()
    {
        $link = $this->getBlock()->getUserGuide();
        if ($link) {
            $seoLink = $this->getSeoparams();
            if (strpos($link, '?') !== false) {
                $seoLink = str_replace('?', '&', $seoLink);
            }

            $link .= $seoLink . 'userguide_' . $this->getModuleCode();
        }

        return $link;
    }

    /**
     * Get SEO parameters
     *
     * @return string
     */
    private function getSeoparams()
    {
        return self::SEO_PARAMS;
    }

    /**
     * Check last version of module feed data
     *
     * @param $currentVer
     * @return bool
     */
    private function isLastVersion($currentVer)
    {
        $result = true;

        $module = $this->getFeedModuleData();
        if ($module
            && isset($module['version'])
            && version_compare($module['version'], (string)$currentVer, '>')
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get module name
     *
     * @return string
     */
    private function getModuleName()
    {
        $result = '';

        $configTabs = $this->configStructure->getTabs();
        if ($name = $this->findResourceName($configTabs)) {
            $result = $name;
        }

        if (!$result) {
            $module = $this->getFeedModuleData();

            if ($module && isset($module['name'])) {
                $result = $module['name'];
                $result = str_replace(' for Magento 2', '', $result);
            }
        }

        if (!$result) {
            $result = __('Extension');
        }

        return $result;
    }

    /**
     * Find the resouce name with module code
     *
     * @param $config
     * @return string
     */
    private function findResourceName($config)
    {
        $result = '';
        $currentNode = null;
        foreach ($config as $key => $node) {
            if ($node->getId() == 'magespark') {
                $currentNode = $node;
                break;
            }
        }

        if ($currentNode) {
            foreach ($currentNode->getChildren() as $item) {
                $data = $item->getData('resource');
                if (isset($data['label'])
                    && isset($data['resource'])
                    && strpos($data['resource'], $this->getModuleCode() . '::') !== false
                ) {
                    $result = $data['label'];
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Get feed module data
     *
     * @return array|null
     */
    private function getFeedModuleData()
    {
        if ($this->moduleData === null) {
            $allExtensions = $this->moduleHelper->getAllExtensions();
            if ($allExtensions && isset($allExtensions[$this->getModuleCode()])) {
                $module = $allExtensions[$this->getModuleCode()];
                if ($module && is_array($module)) {
                    $module = array_shift($module);
                }

                $this->moduleData = $module;
            }
        }

        return $this->moduleData;
    }

    /**
     * Get module link with data
     *
     * @return mixed|string
     */
    private function getModuleLink()
    {
        if (!$this->moduleLink) {
            $this->moduleLink = '';
            $module = $this->getFeedModuleData();
            if ($module && isset($module['url'])) {
                $this->moduleLink = $module['url'];
            }
        }

        return $this->moduleLink;
    }

    /**
     * Get any conflict has occure in module
     *
     * @return array
     */
    private function getExistingConflicts()
    {
        $conflicts = [];
        $module = $this->getFeedModuleData();
        if ($module && isset($module['conflictExtensions'])) {
            $conflictsFromSite = $module['conflictExtensions'];
            $conflictsFromSite = str_replace(' ', '', $conflictsFromSite);
            $conflictsFromSite = explode(',', $conflictsFromSite);
            $conflicts = array_merge($conflicts, $conflictsFromSite);
            $conflicts = array_unique($conflicts);
        }

        return $conflicts;
    }

    /**
     * Shows conflict in module
     *
     * @return string
     */
    private function showModuleExistingConflicts()
    {
        $html = '';
        $messages = [];
        foreach ($this->getExistingConflicts() as $moduleName) {
            if ($this->moduleManager->isEnabled($moduleName)) {
                $messages[] = __(
                    'Incompatibility with the %1. '
                    . 'To avoid the conflicts we strongly recommend turning off the 3rd party mod via the following command: "%2"',
                    $moduleName,
                    'magento module:disable ' . $moduleName
                );
            }
        }

        if (count($messages)) {
            $html = '<div class="magespark-conflicts-title">'
                . __('Problems detected:')
                . '</div>';

            $html .= '<div class="magespark-disable-extensions">';
            foreach ($messages as $message) {
                $html .= '<p class="message message-error">' . $message . '</p>';
            }

            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Get name of the block
     *
     * @return mixed
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Set block name
     *
     * @param mixed $block
     */
    public function setBlock($block)
    {
        $this->block = $block;
    }
}
