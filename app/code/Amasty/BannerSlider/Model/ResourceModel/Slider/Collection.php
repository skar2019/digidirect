<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\ResourceModel\Slider;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Api\Data\SliderInterface;
use Amasty\BannerSlider\Model\Slider;
use Amasty\BannerSlider\Model\ResourceModel\Slider as ResourceSlider;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    const BANNER_IDS = 'banner_ids';
    const BANNER_NAMES = 'banner_names';

    protected function _construct()
    {
        $this->_setIdFieldName(SliderInterface::ID);
        $this->_init(Slider::class, ResourceSlider::class);
    }

    protected function joinBannerTable()
    {
        $bannerTable = $this->getResource()->getTable(BannerInterface::STATIC_TABLE_NAME);
        $this->getSelect()->joinLeft(
            $bannerTable,
            sprintf('%s.id = %s', $bannerTable, SliderInterface::BANNER_ID),
            [
                self::BANNER_IDS => sprintf(
                    'GROUP_CONCAT(%s.%s SEPARATOR ",")',
                    $bannerTable,
                    BannerInterface::ID
                ),
                self::BANNER_NAMES => sprintf(
                    'GROUP_CONCAT(%s.%s SEPARATOR ",")',
                    $bannerTable,
                    BannerInterface::NAME
                )
            ]
        );
    }

    /**
     * @param string|array $columns
     */
    protected function joinRelationTable($columns = '*')
    {
        $relationTable = $this->getResource()->getTable(SliderInterface::RELATION_TABLE_NAME);
        $this->getSelect()->joinLeft(
            $relationTable,
            sprintf('%s.%s = main_table.id', $relationTable, SliderInterface::SLIDER_ID),
            $columns
        );
    }

    /**
     * @param string|array $columns
     */
    public function joinStoreTable($columns = '*', int $storeId = 0)
    {
        $this->getSelect()->joinInner(
            ['absd' => $this->getResource()->getTable(SliderInterface::DYNAMIC_TABLE_NAME)],
            sprintf('absd.id = main_table.id AND absd.store_id = %s', $storeId),
            $columns
        );
    }
}
