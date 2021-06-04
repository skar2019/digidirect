<?php

namespace Ewave\Banner\Plugin\Magento\Widget\Controller\Adminhtml\Widget;

use Ewave\Banner\Block\Widget\StaticWidget\TypeRendererInterface;
use Ewave\Banner\Helper\BannerHelper;
use Ewave\Banner\Helper\IssetTrait;
use Magento\Framework\App\Request\Http;
use Magento\Widget\Controller\Adminhtml\Widget\BuildWidget as MagentoBuildWidget;

/**
 * Assign static template when create banner in wysiwyg if "is_static" is selected
 *
 * @since 3.1.0
 */
class BuildWidget
{
    use IssetTrait;

    /**
     * @var BannerHelper
     */
    protected $bannerHelper;

    /**
     * BuildWidget constructor.
     *
     * @param BannerHelper $bannerHelper
     */
    public function __construct(BannerHelper $bannerHelper)
    {
        $this->bannerHelper = $bannerHelper;
    }

    /**
     * @param MagentoBuildWidget $buildWidget
     * @return null|void
     */
    public function beforeExecute(MagentoBuildWidget $buildWidget)
    {
        /**
         * @var $request Http
         */
        $request = $buildWidget->getRequest();
        $widgetType = $request->getParam('widget_type');
        if (!$this->bannerHelper->isBannerWidget($widgetType)) {
            return;
        }
        $parameters = $request->getParam('parameters', []);
        $this->modifyCustom($parameters);
        $this->modifyStatic($parameters);
        $this->finalCheck($parameters);
        $request->setPostValue('parameters', $parameters);
        return null;
    }

    /**
     * @param array $parameters
     * @return void
     */
    protected function finalCheck(array &$parameters)
    {
        if (!$this->isStatic($parameters) && !$this->isCustom($parameters)) {
            $this->setTemplate($parameters, 'Ewave_Banner::widget/block.phtml');
        }
    }

    /**
     * @param array $parameters
     * @return bool
     */
    private function isStatic(array $parameters)
    {
        return $this->returnBoolFromArray($parameters, 'is_static_template');
    }

    /**
     * @param array $parameters
     * @return bool
     */
    private function isCustom(array $parameters)
    {
        return $this->returnBoolFromArray($parameters, 'custom_template');
    }

    /**
     * @param array $array
     * @param string $key
     * @return bool
     */
    private function returnBoolFromArray(array $array, $key)
    {
        return (bool)($this->getByKey($array, $key, false));
    }

    /**
     * @param array $parameters
     * @return void
     */
    private function modifyStatic(array &$parameters)
    {
        if ($this->isCustom($parameters)) {
            return;
        }
        if (!$this->isStatic($parameters)) {
            return;
        }
        $this->setTemplate($parameters, TypeRendererInterface::DEFAULT_STATIC_TEMPLATE);
    }

    /**
     * @param array $parameters
     * @param string $template
     * @return void
     */
    private function setTemplate(array &$parameters, $template)
    {
        $parameters['template'] = $template;
    }

    /**
     * @param array $parameters
     * @return void
     */
    private function modifyCustom(array &$parameters)
    {
        if (!$this->isCustom($parameters)) {
            return;
        }
        $template = $this->getByKey($parameters, 'custom_template', null);
        $this->setTemplate($parameters, 'Ewave_Banner::widget/' . $template);
    }
}
