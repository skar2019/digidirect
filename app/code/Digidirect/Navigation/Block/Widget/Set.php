<?php

namespace Digidirect\Navigation\Block\Widget;

use Digidirect\Navigation\Block\MenuBlockInterface;
use Digidirect\Navigation\Model\Frontend\CustomerSetInterface;
use Digidirect\Navigation\Model\Frontend\CustomerSetMaker;
use Digidirect\Navigation\Model\Frontend\MenuSetData;
use Digidirect\Navigation\Model\Frontend\MenuSetDataFormatter;
use Digidirect\Navigation\Model\UrlComparator;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * Menu set widget
 *
 * @since 1.3.0
 * @api
 */
class Set extends Template implements BlockInterface, MenuBlockInterface, IdentityInterface
{
    const CUSTOM_TEMPLATE = 'custom_template';
    const TEMPLATE_EXTENSION = '.phtml';
    const TEMPLATE_PATH = '::widget/set/';
    const SETTINGS = 'settings';
    const EXPLODE_ELEMENT = ';';
    const EXPLODE_SETTING_ELEMENT = ':';

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var MenuSetData
     */
    protected $menuSetData;

    /**
     * @var MenuSetDataFormatter
     */
    protected $menuDataFormatter;

    /**
     * @var CustomerSetMaker
     */
    protected $customerSetMaker;

    /**
     * @var null
     */
    protected $currentUrl = null;

    /**
     * @var null|UrlComparator
     */
    protected $urlComparator;

    /**
     * Set constructor.
     *
     * @param Template\Context $context
     * @param MenuSetData $menuSetData
     * @param MenuSetDataFormatter $menuSetDataFormatter
     * @param CustomerSetMaker $customerSetMaker
     * @param UrlComparator $urlComparator
     * @param string $template
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        MenuSetData $menuSetData,
        MenuSetDataFormatter $menuSetDataFormatter,
        CustomerSetMaker $customerSetMaker,
        UrlComparator $urlComparator,
        string $template = 'Digidirect_Navigation::widget/default.phtml',
        array $data = []
    ) {
        $this->_template = $template;
        $this->menuDataFormatter = $menuSetDataFormatter;
        $this->menuSetData = $menuSetData;
        $this->customerSetMaker = $customerSetMaker;
        $this->urlComparator = $urlComparator;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $customTemplate = $this->getData(self::CUSTOM_TEMPLATE);
        $customTemplate = trim($customTemplate);
        if ($customTemplate) {
            $templatePath = $customTemplate . self::TEMPLATE_EXTENSION;
            $moduleName = $this->getModuleName();
            $params = ['module' => $moduleName];
            $area = $this->getArea();
            if ($area) {
                $params['area'] = $area;
            }

            $fullTemplatePath = $moduleName . self::TEMPLATE_PATH . $templatePath;
            $path = $this->resolver->getTemplateFileName($fullTemplatePath, $params);

            if ($path) {
                $this->setTemplate($fullTemplatePath);
            }
        }
        return parent::_toHtml();
    }

    /**
     * Get cache key info
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getCacheKeyInfo()
    {
        $customTemplate = $this->getData(self::CUSTOM_TEMPLATE);
        $customTemplate = trim($customTemplate);
        $templatePath = $customTemplate . self::TEMPLATE_EXTENSION;
        return [
            'BLOCK_TPL',
            $this->getCustomerSet()->getStoreCode(),
            $this->getTemplateFile(),
            'base_url' => $this->getBaseUrl(),
            'is_logged_in' => $this->getCustomerSet()->isLoggedIn(),
            'set_code' => $this->getData('set_code'),
            'url' => $this->getCurrentUrl(),
            $customTemplate,
            $templatePath,
        ];
    }

    /**
     * @return \Digidirect\Navigation\Model\Frontend\CustomerSetInterface
     */
    protected function getCustomerSet(): CustomerSetInterface
    {
        return $this->customerSetMaker->makeCustomerSet($this->getData('set_code'));
    }

    /**
     * @param string $setting
     * @return mixed|null
     */
    public function getSetting(string $setting)
    {
        if (empty($this->settings)) {
            $settings = explode(self::EXPLODE_ELEMENT, $this->getData(self::SETTINGS));
            if (!empty($settings)) {
                foreach ($settings as $settingString) {
                    $settingArray = explode(self::EXPLODE_SETTING_ELEMENT, $settingString);
                    $key = $settingArray[0] ?? null;
                    $value = $settingArray[1] ?? null;
                    $this->settings[$key] = $value;
                }
            }
        }
        return $this->settings[$setting] ?? null;
    }

    /**
     * Force set setting. Method is public because sometimes it is possible that we need to overwrite setting
     * in template
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setSetting(string $key, $value)
    {
        $this->settings[$key] = $value;
        return $this;
    }

    /**
     * Safe method for not overwriting existing widget setting
     * Set setting value If widget does not have setting
     *
     * @param string $key
     * @param mixed $value
     * @return Set
     */
    public function setDefaultSetting(string $key, $value): self
    {
        if (!$this->getSetting($key)) {
            $this->setSetting($key, $value);
        }
        return $this;
    }

    /**
     * @param string $settingCode
     * @param mixed $returnPresented
     * @param mixed $returnNotPresented
     * @return mixed
     */
    public function conditionalSettingReturn(string $settingCode, $returnPresented, $returnNotPresented)
    {
        return $this->getSetting($settingCode) ? $returnPresented : $returnNotPresented;
    }

    /**
     * @param array $array
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function returnIfIsset(array $array, string $key, $default = null)
    {
        return $array[$key] ?? $default;
    }

    /**
     * @param string $settingCode
     * @return mixed
     */
    public function returnSettingItselfIfPresented(string $settingCode, $default = null)
    {
        return $this->conditionalSettingReturn($settingCode, $this->getSetting($settingCode), $default);
    }

    /**
     * @return bool|string
     */
    public function getMenuJsonArray()
    {
        return $this->menuDataFormatter->toJson($this->getMenuCollection());
    }

    /**
     * @return array
     */
    public function getMenuAsArray(): array
    {
        return $this->menuDataFormatter->toArray($this->getMenuCollection());
    }

    /**
     * @return object
     */
    public function getMenuJsonObject(): object
    {
        return $this->menuDataFormatter->toObjectArray(
            $this->getMenuCollection()
        );
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    protected function getMenuCollection(): DataObject
    {
        return $this->menuSetData->getSetByCode(
            $this->customerSetMaker->makeCustomerSet($this->getData('set_code'))
        );
    }

    /**
     * @return array|mixed
     */
    public function getIdentities()
    {
        return $this->menuSetData->getIdentities($this->getCustomerSet());
    }

    /**
     * @return int
     */
    public function getCacheLifetime()
    {
        return parent::getCacheLifetime() ?: 864000;
    }

    /**
     * @return string
     */
    protected function getCurrentUrl(): string
    {
        if (null === $this->currentUrl) {
            $this->currentUrl = $this->_urlBuilder->getCurrentUrl();
        }

        return $this->currentUrl;
    }

    /**
     * Can be used if needed in template
     *
     * @param string $url
     * @param null $param
     * @return mixed
     */
    public function parseUrl($url, $param = null)
    {
        return $this->urlComparator->getParseUrlResultByUrl($url, $param);
    }
}
