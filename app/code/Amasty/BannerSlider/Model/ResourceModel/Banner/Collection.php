<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\ResourceModel\Banner;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Api\Data\SliderInterface;
use Amasty\BannerSlider\Model\Banner;
use Amasty\BannerSlider\Model\ResourceModel\Banner as ResourceBanner;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_setIdFieldName(BannerInterface::ID);
        $this->_init(Banner::class, ResourceBanner::class);
        $this->addFilterToMap(BannerInterface::ID, 'main_table.' . BannerInterface::ID);
    }

    public function addDynamicData(): Collection
    {
        $dynamicContentTable = $this->getResource()->getTable(BannerInterface::DYNAMIC_TABLE_NAME);
        $this->getSelect()->joinInner(
            $dynamicContentTable,
            sprintf(
                '%s.id = main_table.id AND %s.store_id = %s',
                $dynamicContentTable,
                $dynamicContentTable,
                Store::DEFAULT_STORE_ID
            ),
            BannerInterface::DYNAMIC_FIELDS
        );

        return $this;
    }

    public function addDynamicTable(int $storeId = 0, array $columns = BannerInterface::DYNAMIC_FIELDS)
    {
        $dynamicContentTable = $this->getTable(BannerInterface::DYNAMIC_TABLE_NAME);

        $this->joinDynamicTable($dynamicContentTable, Store::DEFAULT_STORE_ID);
        if ($storeId !== Store::DEFAULT_STORE_ID) {
            $this->joinDynamicTable($dynamicContentTable, $storeId);
            $this->selectColumns($dynamicContentTable, $columns, $storeId);
        }
    }

    public function addPositionData(int $sliderId): void
    {
        $relationTable = $this->getTable(SliderInterface::RELATION_TABLE_NAME);
        $this->getSelect()->join(
            $relationTable,
            sprintf(
                '(%1$s.%2$s = %3$d) and (%1$s.%4$s = main_table.%5$s)',
                $relationTable,
                SliderInterface::SLIDER_ID,
                $sliderId,
                SliderInterface::BANNER_ID,
                BannerInterface::ID
            ),
            [SliderInterface::POSITION]
        );
    }

    private function joinDynamicTable(string $dynamicContentTable, int $storeId = 0)
    {
        $alias = sprintf('%s_%s', BannerInterface::DYNAMIC_TABLE_NAME, $storeId);
        $this->getSelect()->joinLeft(
            [$alias => $dynamicContentTable],
            sprintf('%s.id = main_table.id AND %s.store_id = %s', $alias, $alias, $storeId)
        );
    }

    private function selectColumns(string $dynamicContentTable, array $columns, int $storeId = 0)
    {
        $currentColumns = [];
        foreach ($columns as $column) {
            $currentColumns[$column] = $this->getConnection()->getIfNullSql(
                sprintf('%s_%s.%s', BannerInterface::DYNAMIC_TABLE_NAME, $storeId, $column),
                sprintf('%s_%s.%s', BannerInterface::DYNAMIC_TABLE_NAME, Store::DEFAULT_STORE_ID, $column)
            );
        }
        $this->getSelect()->columns($currentColumns);
    }

    /**
     * @param int[]|string $ids
     */
    public function addOrderByIds($ids): void
    {
        $ids = is_array($ids) ? join(',', $ids) : $ids;
        $this->getSelect()->order(new \Zend_Db_Expr(sprintf(
            'FIELD(main_table.%s, %s)',
            $this->getIdFieldName(),
            $ids
        )));
    }

    public function getBannersForScheduler(): Collection
    {
        $this->getSelect()->where(
            sprintf(
                '%s = CURDATE() OR %s = CURDATE() - INTERVAL 1 DAY',
                BannerInterface::START_DATE,
                BannerInterface::END_DATE
            )
        );

        return $this;
    }
}
