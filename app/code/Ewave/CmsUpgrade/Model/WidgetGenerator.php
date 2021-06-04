<?php

namespace Ewave\CmsUpgrade\Model;

use Magento\Widget\Model\Widget\Instance;
use Magento\Widget\Model\Widget\InstanceFactory;
use Ewave\CmsUpgrade\Helper\MapperWidget;
use Ewave\CmsUpgrade\Model\Widget\ThemeResolver;

/**
 * Class CmsGenerator
 *
 * @package Ewave\CmsUpgrade\Model
 */
class WidgetGenerator extends Generator
{
    /**
     * @var \Magento\Widget\Model\Widget\InstanceFactory
     */
    protected $_widgetFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_storeManager;

    /**
     * @var \Ewave\CmsUpgrade\Helper\MapperWidget
     */
    protected $_helperMapper;

    /**
     * @var ThemeResolver
     */
    protected $themeResolver;

    /**
     * WidgetGenerator constructor.
     *
     * @param GeneratorContext $context
     * @param InstanceFactory $widgetFactory
     * @param MapperWidget $helperMapper
     * @param ThemeResolver $themeResolver
     */
    public function __construct(
        GeneratorContext $context,
        InstanceFactory $widgetFactory,
        MapperWidget $helperMapper,
        ThemeResolver $themeResolver
    ) {
        parent::__construct($context);
        $this->_widgetFactory = $widgetFactory;
        $this->_helperMapper = $helperMapper;
        $this->themeResolver = $themeResolver;
    }

    /**
     * @param array $widgetIds
     * @return \Magento\Framework\DataObject
     */
    public function processUpgradeScript(array $widgetIds)
    {
        $data = $this->_getUpgradeData();
        foreach ($widgetIds as $widgetId) {
            $widget = $this->_widgetFactory
                ->create()
                ->load($widgetId, 'instance_id');

            $widgetData = $this->_prepareWidgetData($widget);
            $data['items'][] = $widgetData;
        }
        $nextVersion = $this->_getNextModuleVersion();
        $put = $this->putUpgradeFile($data, $nextVersion);
        if ($put) {
            $this->_changeDbVersion($nextVersion);
        }
        return $this->_result;
    }

    /**
     * @param Instance $widget
     * @return array
     */
    protected function _prepareWidgetData(Instance $widget)
    {
        $widgetData = $this->_fillData($widget);
        $widgetData = $this->_prepareWidgetGroup($widgetData);
        $widgetData = $this->_prepareWidgetParams($widgetData);
        $widgetData = $this->prepareTheme($widgetData);

        return $widgetData;
    }

    /**
     * @param [] $widgetData
     * @return mixed
     */
    protected function prepareTheme(array $widgetData)
    {
        $themeId = $widgetData['theme_id'] ?? null;
        $themeCode = $this->themeResolver->getThemeCodeById($themeId);
        $widgetData['theme_id'] = $themeCode;
        return $widgetData;
    }

    /**
     * Prepare data for save group widget
     *
     * @param array $widgetData
     * @return array
     */
    protected function _prepareWidgetGroup(array $widgetData)
    {
        if (!empty($widgetData['page_groups'])) {
            foreach ($widgetData['page_groups'] as $keyGroup => $item) {
                $tmpItem = [];
                if (isset($item['page_group'])) {
                    $tmpItem['page_group'] = $item['page_group'];
                    $tmpItem[$item['page_group']] =
                        [
                            'page_id' => $item['page_id'] ?? null,
                            'layout_handle' => $item['layout_handle'] ?? null,
                            'block' => $item['block_reference'] ?? null,
                            'for' => $item['page_for'] ?? null,
                            'entities' => $item['entities'] ?? null,
                            'template' => $item['page_template'] ?? null,
                        ];

                    $widgetData['page_groups'][$keyGroup] = $tmpItem;
                }
            }
        }
        return $widgetData;
    }

    /**
     * Prepare data of widget params
     *
     * @param array $widgetData
     * @return array
     */
    protected function _prepareWidgetParams(array $widgetData)
    {
        $widgetData['widget_parameters'] = $this->_helperMapper->runProcessor(
            MapperWidget::PRE_PROCESSOR_PREFIX,
            $widgetData['widget_parameters'],
            $widgetData['instance_type']
        );

        return $widgetData;
    }
}
