<?php

namespace Ewave\CmsUpgrade\Console\Command\Processor;

use Ewave\CmsUpgrade\Model\Widget\ThemeResolver;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Widget\Model\ResourceModel\Widget\Instance\Collection;
use Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory;
use Ewave\CmsUpgrade\Helper\MapperWidget;
use Magento\Widget\Model\Widget\Instance;

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
     * @var MapperWidget
     */
    protected $_helperMapper;

    /**
     * @var ThemeResolver
     */
    protected $themeResolver;

    /**
     * @var State
     */
    protected $appState;

    /**
     * WidgetProcessor constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param MapperWidget      $helperMapper
     * @param ThemeResolver     $themeResolver
     * @param State             $appState
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        MapperWidget $helperMapper,
        ThemeResolver $themeResolver,
        State $appState
    ) {
        $this->themeResolver = $themeResolver;
        $this->_collectionFactory = $collectionFactory;
        $this->_helperMapper = $helperMapper;
        $this->appState = $appState;
    }

    /**
     * @param array $data
     * @return Instance
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

        /** @var Collection $widgetCollection */
        $widgetCollection = $this->_collectionFactory->create();

        /** @var Instance $widget */
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

    /**
     * Emulate frontend area code for afterSave widget model
     * @see \Magento\Widget\Model\ResourceModel\Widget\Instance::_afterSave
     * @param bool $checkUpdateDate
     *
     * @throws \Exception
     */
    public function upgrade($checkUpdateDate = false)
    {
        $this->appState->emulateAreaCode(
            Area::AREA_FRONTEND,
            function () use ($checkUpdateDate) {
                parent::upgrade($checkUpdateDate);
            },
            [$checkUpdateDate]
        );
    }
}