<?php
namespace Ewave\CmsUpgrade\Helper;

use Ewave\CmsUpgrade\Console\Command\Processor\BannerProcessor;
use Magento\Banner\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Data
 * @package Ewave\CmsUpgrade\Helper
 */
class MapperWidget extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PRE_PROCESSOR_PREFIX = 'process';

    const POST_PROCESSOR_PREFIX = 'post';

    const BANNER_ENTITY_ID = 'banner_id';
    const BANNER_UNIQ_KEY = 'banner_ids';
    const TEMP_BANNER_UNIQ_KEY = 'banner_uniqs';

    /**
     * @var \Magento\Banner\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $_bannerCollectionFactory;

    /**
     * MapperWidget constructor.
     * @param Context $context
     * @param CollectionFactory $bannerCollectionFactory
     */
    public function __construct(
        Context $context,
        CollectionFactory $bannerCollectionFactory
    ) {
        $this->_bannerCollectionFactory = $bannerCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Mapper for additional processing widget params
     *
     * @var array
     */
    const MAP_WIDGET_INSTANCE_TYPE = [
        'Magento\Banner\Block\Widget\Banner' => 'magentoBanner',
    ];

    /**
     * @param $step
     * @param $dataWidget
     * @param $widgetTypeClass
     * @return mixed
     */
    public function runProcessor($step, $dataWidget, $widgetTypeClass)
    {
        $aMapperType = self::MAP_WIDGET_INSTANCE_TYPE;
        if (!empty($dataWidget) && array_key_exists($widgetTypeClass, $aMapperType)) {
            $methodName = sprintf($step . '%s', ucfirst($aMapperType[$widgetTypeClass]));
            $dataWidget = $this->$methodName($dataWidget);
        }
        return $dataWidget;
    }

    /**
     * @param $method
     * @param $args
     * @return array|mixed
     */
    public function __call($method, $args)
    {
        return !empty($args) ? current($args) : [];
    }

    /**
     * @param array $widgetParams
     * @return array
     */
    protected function processMagentoBanner($widgetParams = [])
    {
        if (!empty($widgetParams)) {
            if (!empty($widgetParams[self::BANNER_UNIQ_KEY])) {
                $bannerIds = explode(',', (string)$widgetParams[self::BANNER_UNIQ_KEY]);
                $bannerCollection = $this->_bannerCollectionFactory->create();
                $bannerCollection
                    ->addFieldToSelect(BannerProcessor::PK_FIELD)
                    ->addFieldToFilter(self::BANNER_ENTITY_ID, [
                        'in' => $bannerIds
                    ]);
                $bannerCollection = $this->orderByField(
                    $bannerCollection,
                    self::BANNER_ENTITY_ID,
                    $bannerIds
                );
                if ($bannerCollection->getSize()) {
                    $widgetParams[self::TEMP_BANNER_UNIQ_KEY] =
                        $bannerCollection->getColumnValues(BannerProcessor::PK_FIELD);
                }
            }
        }

        return $widgetParams;
    }

    /**
     * @param array $widgetParams
     * @return array
     */
    protected function postMagentoBanner($widgetParams = [])
    {
        if (!empty($widgetParams)) {
            if (!empty($widgetParams[self::TEMP_BANNER_UNIQ_KEY])
                && is_array($widgetParams[self::TEMP_BANNER_UNIQ_KEY])
            ) {
                $bannerCollection = $this->_bannerCollectionFactory->create();
                $bannerCollection
                    ->addFieldToSelect(self::BANNER_ENTITY_ID)
                    ->addFieldToFilter(BannerProcessor::PK_FIELD, [
                        'in' => $widgetParams[self::TEMP_BANNER_UNIQ_KEY]
                    ]);
                $bannerCollection = $this->orderByField(
                    $bannerCollection,
                    BannerProcessor::PK_FIELD,
                    $widgetParams[self::TEMP_BANNER_UNIQ_KEY]
                );
                if ($bannerCollection->getSize()) {
                    $widgetParams[self::BANNER_UNIQ_KEY] = implode(
                        ',',
                        $bannerCollection->getColumnValues(self::BANNER_ENTITY_ID)
                    );
                }
            }
        }

        return $widgetParams;
    }

    /**
     * Generate order by field for save sequency banners
     *
     * @param AbstractCollection $collection
     * @param $field
     * @param array $ids
     * @return AbstractCollection
     */
    protected function orderByField(AbstractCollection $collection, $field, array $ids)
    {
        $select = $collection->getSelect();
        $adapter = $select->getAdapter();
        $ids = $adapter->quoteInto('?', $ids);
        $field = $adapter->quoteIdentifier($field);
        if ($field && $ids) {
            $select->order(new \Zend_Db_Expr("FIELD({$field}, {$ids})"));
        }
        return $collection;
    }
}
