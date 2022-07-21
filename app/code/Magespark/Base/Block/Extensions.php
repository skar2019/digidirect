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

namespace MageSpark\Base\Block;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\View\Helper\Js;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\View\LayoutFactory;
use MageSpark\Base\Helper\Module;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\FileSystemException;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\BlockInterface;

/**
 * Class Extensions
 *
 * @package MageSpark\Base\Block
 */
class Extensions extends Fieldset
{
    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var Module
     */
    protected $moduleHelper;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Extensions constructor.
     *
     * @param Context $context
     * @param Session $authSession
     * @param Js $jsHelper
     * @param ModuleListInterface $moduleList
     * @param LayoutFactory $layoutFactory
     * @param Module $moduleHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        ModuleListInterface $moduleList,
        LayoutFactory $layoutFactory,
        Module $moduleHelper,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);

        $this->moduleList    = $moduleList;
        $this->layoutFactory = $layoutFactory;
        $this->moduleHelper  = $moduleHelper;
        $this->scopeConfig   = $context->getScopeConfig();
    }

    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     * @return string
     * @throws FileSystemException
     */
    public function render(AbstractElement $element)
    {
        $html = $this->_getHeaderHtml($element);

        $modules = $this->moduleList->getNames();

        $dispatchResult = new DataObject($modules);
        $modules = $dispatchResult->toArray();

        sort($modules);
        foreach ($modules as $moduleName) {
            if (strstr($moduleName, 'MageSpark_') === false
                || $moduleName === 'MageSpark_Base'
                || in_array($moduleName, $this->moduleHelper->getRestrictedModules(), true)
            ) {
                continue;
            }

            $html .= $this->_getFieldHtml($element, $moduleName);
        }

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    /**
     * Getting the Renderer field
     *
     * @return BlockInterface
     */
    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $layout = $this->layoutFactory->create();

            $this->_fieldRenderer = $layout->createBlock(
                Field::class
            );
        }

        return $this->_fieldRenderer;
    }

    /**
     * Read info about extension from composer json file
     *
     * @param $moduleCode
     * @return array|mixed
     */
    protected function _getModuleInfo($moduleCode)
    {
        return $this->moduleHelper->getModuleInfo($moduleCode);
    }

    /**
     * Get the version and description of the module
     *
     * @param $fieldset
     * @param $moduleCode
     * @return string
     */
    protected function _getFieldHtml($fieldset, $moduleCode)
    {
        $module = $this->_getModuleInfo($moduleCode);

        if (!is_array($module) ||
           !array_key_exists('version', $module) ||
           !array_key_exists('description', $module)
        ) {
            return '';
        }

        $currentVer = $module['version'];
        $moduleName = $module['description'];
        $moduleName = $this->_replaceMageSparkText($moduleName);
        $status =
             '<a target="_blank">
                <img src="'. $this->getViewFileUrl('MageSpark_Base::images/ok.gif') . '" title="' . __("Installed") . '"/>
             </a>';

        $allExtensions = $this->moduleHelper->getAllExtensions();
        if ($allExtensions && isset($allExtensions[$moduleCode])) {
            $singleRecord = array_key_exists('name', $allExtensions[$moduleCode]);
            $ext = $singleRecord ? $allExtensions[$moduleCode] : end($allExtensions[$moduleCode]);

            $url     = $ext['url'];
            $name    = $ext['name'];
            $name = $this->_replaceMageSparkText($name);
            $lastVer = $ext['version'];

            $moduleName =
                '<a href="' . $url . '" target="_blank" title="' . $name . '">'
                    . $name .
                '</a>';

            if (version_compare($currentVer, $lastVer, '<')) {
                $status =
                    '<a href="' . $url . '" target="_blank">
                        <img src="' . $this->getViewFileUrl('MageSpark_Base::images/update.gif') .
                            '" alt="' . __("Update available") . '" title="'. __("Update available")
                    .'"/></a>';
            }
        }

        // in case if module output disabled
        if ($this->scopeConfig->getValue('advanced/modules_disable_output/' . $moduleCode)) {
            $href = isset($url) ? ' href="' . $url . '"' : '';
            $status =
                '<a' . $href . ' target="_blank">
                    <img src="' . $this->getViewFileUrl('MageSpark_Base::images/bad.gif') .
                '" alt="' . __("Output disabled") . '" title="'. __("Output disabled")
                .'"/></a>';
        }

        $moduleName = $status . ' ' . $moduleName;

        $field = $fieldset->addField($moduleCode, 'label', [
            'name'  => 'dummy',
            'label' => $moduleName,
            'value' => $currentVer,
        ])->setRenderer($this->_getFieldRenderer());

        return $field->toHtml();
    }

    /**
     * Replace the text of the module
     *
     * @param $moduleName
     * @return mixed
     */
    protected function _replaceMageSparkText($moduleName)
    {
        $moduleName = str_replace('for Magento 2', '', $moduleName);
        $moduleName = str_replace('by MageSpark', '', $moduleName);

        return $moduleName;
    }
}
