<?php

namespace Ewave\CmsUpgrade\Console\Command\Processor;

use Ewave\CmsUpgrade\Model\Widget\ThemeResolver;
use Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory;
use Ewave\CmsUpgrade\Helper\MapperWidget;

/**
 * Class WidgetProcessor
 *
 * @package Ewave\CmsUpgrade\Setup\Processor
 */
class WidgetProcessor extends AbstractProcessor
{
    const PK_FIELD = 'title';
    const PK_FIELD_ADD = 'instance_type';

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Ewave\CmsUpgrade\Helper\MapperWidget
     */
    protected $_helperMapper;

    /**
     * @var ThemeResolver
     */
    protected $themeResolver;

    /**
     * WidgetProcessor constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param MapperWidget $helperMapper
     * @param ThemeResolver $themeResolver
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        MapperWidget $helperMapper,
        ThemeResolver $themeResolver
    ) {
        $this->themeResolver = $themeResolver;
        $this->_collectionFactory = $collectionFactory;
        $this->_helperMapper = $helperMapper;
    }

    /**
     * @param array $data
     * @return \Magento\Widget\Model\Widget\Instance
     */
    protected function _prepareModel(array $data)
    {
        if (empty($data[static::PK_FIELD]) || empty($data[static::PK_FIELD_ADD])) {
            return false;
        }

        /**
         * Fix for different ids on different environments
         */
        $themeId = $data['theme_id'] ?? null;
        $themeIdFromDb = $this->themeResolver->getThemeIdByCode($themeId);
        if ($themeIdFromDb) {
            $data['theme_id'] = $themeIdFromDb;
        }

        /** @var \Magento\Widget\Model\ResourceModel\Widget\Instance\Collection $widgetCollection */
        $widgetCollection = $this->_collectionFactory->create();

        /** @var \Magento\Widget\Model\Widget\Instance $widget */
        $widget = $widgetCollection
            ->addFieldToFilter(static::PK_FIELD, $data[static::PK_FIELD])
            ->addFieldToFilter(static::PK_FIELD_ADD, $data[static::PK_FIELD_ADD])
            ->addFieldToFilter('theme_id', $themeIdFromDb)
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();

        $data['widget_parameters'] = $this->_helperMapper->runProcessor(
            MapperWidget::POST_PROCESSOR_PREFIX,
            $data['widget_parameters'],
            $data[static::PK_FIELD_ADD]
        );
        $widget->addData($data);

        return $widget;
    }
}