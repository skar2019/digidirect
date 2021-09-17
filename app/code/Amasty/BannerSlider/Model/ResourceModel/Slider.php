<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\ResourceModel;

use Amasty\BannerSlider\Api\Data\SliderInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\Store;

class Slider extends AbstractDb
{
    protected function _construct()
    {
        $this->_init(SliderInterface::STATIC_TABLE_NAME, SliderInterface::ID);
    }

    protected function _afterSave(AbstractModel $object): AbstractDb
    {
        $this->saveDynamicData($object);
        if ($object->getData(SliderInterface::BANNERS)) {
            $this->saveBanners($object);
        }

        return parent::_afterSave($object);
    }

    private function saveDynamicData(AbstractModel $object)
    {
        $connection = $this->getConnection();
        $table = $this->getTable(SliderInterface::DYNAMIC_TABLE_NAME);

        $connection->insertOnDuplicate(
            $table,
            $object->getDynamicData()
        );
    }

    private function saveBanners(AbstractModel $object)
    {
        if ($object->getData(SliderInterface::BANNER_IDS)) {
            $origBannerIds = $object->getData(SliderInterface::BANNER_IDS);
            $origBannerIds = is_array($origBannerIds) ?: explode(',', $origBannerIds);
        } else {
            $origBannerIds = [];
        }

        $bannerIds = $this->insertRelations($object);
        $oldRelations = array_diff($origBannerIds, $bannerIds);
        $this->deleteOldRelations($object, $oldRelations);
    }

    private function insertRelations(AbstractModel $object): array
    {
        if (isset($object->getData(SliderInterface::BANNERS)[SliderInterface::BANNER_DATA])) {
            $insertData = [];
            foreach ($object->getData(SliderInterface::BANNERS)[SliderInterface::BANNER_DATA] as $banner) {
                $bannerIds[] = $banner[SliderInterface::ID];
                $insertData[] = [
                    SliderInterface::SLIDER_ID => $object->getData(SliderInterface::ID),
                    SliderInterface::BANNER_ID => $banner[SliderInterface::ID],
                    SliderInterface::POSITION => $banner[SliderInterface::POSITION]
                ];
            }
            if (!empty($insertData)) {
                $this->getConnection()->insertOnDuplicate(
                    $this->getTable(SliderInterface::RELATION_TABLE_NAME),
                    $insertData
                );
            }
        }

        return $bannerIds ?? [];
    }

    private function deleteOldRelations(AbstractModel $object, array $oldRelations)
    {
        if (!empty($oldRelations)) {
            $this->getConnection()->delete(
                $this->getTable(SliderInterface::RELATION_TABLE_NAME),
                [
                    sprintf('%s=%s', SliderInterface::SLIDER_ID, $object->getData(SliderInterface::ID)),
                    sprintf('%s IN (%s)', SliderInterface::BANNER_ID, implode(', ', $oldRelations))
                ]
            );
        }
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param AbstractModel $object
     *
     * @return Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $mainTable = $this->getTable(SliderInterface::STATIC_TABLE_NAME);
        $select = $this->joinStoreTable($select, $object);
        $select = $this->joinRelationTable($select);
        $select = $select->group(
            [
                sprintf('%s.%s', $mainTable, SliderInterface::ID)
            ]
        );
        $select->columns(sprintf(
            '%s AS %s',
            $this->getTable(SliderInterface::STATIC_TABLE_NAME) . '.' . SliderInterface::ID,
            SliderInterface::ID
        ));

        return $select;
    }

    /**
     * @param Select $select
     * @param string $columns
     * @return Select
     */
    private function joinRelationTable(Select $select)
    {
        $mainTable = $this->getTable(SliderInterface::STATIC_TABLE_NAME);
        $relationTable = $this->getTable(SliderInterface::RELATION_TABLE_NAME);
        $select->joinLeft(
            $relationTable,
            sprintf('%s.%s = %s.id', $relationTable, SliderInterface::SLIDER_ID, $mainTable),
            [
                SliderInterface::BANNER_IDS => sprintf(
                    'GROUP_CONCAT(%s.%s ORDER BY %s SEPARATOR ",")',
                    $relationTable,
                    SliderInterface::BANNER_ID,
                    SliderInterface::POSITION
                )
            ]
        );

        return $select;
    }

    /**
     * @param Select $select
     * @param AbstractModel $object
     * @return Select
     */
    private function joinStoreTable(Select $select, AbstractModel $object)
    {
        $dynamicContentTable = $this->getTable(SliderInterface::DYNAMIC_TABLE_NAME);
        $storeId = (int) $object->getData(SliderInterface::STORE_ID);

        $select = $this->joinDynamicTable($select, $dynamicContentTable, Store::DEFAULT_STORE_ID);
        if ($storeId !== Store::DEFAULT_STORE_ID) {
            $select = $this->joinDynamicTable($select, $dynamicContentTable, $storeId);
            $select = $this->selectColumns($select, SliderInterface::DYNAMIC_FIELDS, $storeId);
        }

        return $select;
    }

    private function joinDynamicTable(Select $select, string $dynamicContentTable, int $storeId = 0): Select
    {
        $alias = sprintf('%s_%s', SliderInterface::DYNAMIC_TABLE_NAME, $storeId);
        $select->joinLeft(
            [$alias => $dynamicContentTable],
            sprintf('%s.id = %s.id AND %s.store_id = %s', $alias, $this->getMainTable(), $alias, $storeId)
        );

        return $select;
    }

    private function selectColumns(Select $select, array $columns, int $storeId = 0): Select
    {
        $currentColumns = [];
        foreach ($columns as $column) {
            $currentColumns[$column] = $this->getConnection()->getIfNullSql(
                sprintf('%s_%s.%s', SliderInterface::DYNAMIC_TABLE_NAME, $storeId, $column),
                sprintf('%s_%s.%s', SliderInterface::DYNAMIC_TABLE_NAME, Store::DEFAULT_STORE_ID, $column)
            );
        }

        return $select->columns($currentColumns);
    }
}
