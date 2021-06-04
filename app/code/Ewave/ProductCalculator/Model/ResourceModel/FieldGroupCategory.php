<?php

namespace Ewave\ProductCalculator\Model\ResourceModel;

use Ewave\ProductCalculator\Api\Data\FieldGroupCategoryInterface;
use Ewave\ProductCalculator\Api\Data\FieldGroupInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class FieldGroupCategory
 * @package Ewave\ProductCalculator\Model\ResourceModel
 */
class FieldGroupCategory extends AbstractDb
{
    const FIELD_GROUP_CATEGORY_TABLE = 'ewave_productcalculator_field_group_category';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::FIELD_GROUP_CATEGORY_TABLE, FieldGroupCategoryInterface::ID);
    }

    /**
     * Save field group relations
     *
     * @param \Ewave\ProductCalculator\Model\FieldGroupCategory $fieldGroupCategory
     * @return $this
     */
    public function saveFieldGroups($fieldGroupCategory)
    {
        $categoryId = $fieldGroupCategory->getId();
        $fieldGroups = $fieldGroupCategory->getData('links/field_groups') ?? [];
        if ($categoryId) {
            $connection = $this->getConnection();
            $connection->update(
                FieldGroup::FIELD_GROUP_TABLE,
                [FieldGroupInterface::CATEGORY_ID => null],
                [FieldGroupInterface::CATEGORY_ID . ' = ?' => $categoryId]
            );

            $fieldGroupIds = [];
            foreach ($fieldGroups as $fieldGroup) {
                $fieldGroupIds[] = $fieldGroup['id'];
            }
            if (!empty($fieldGroupIds)) {
                $connection->update(
                    FieldGroup::FIELD_GROUP_TABLE,
                    [FieldGroupInterface::CATEGORY_ID => $categoryId],
                    [FieldGroupInterface::ID . ' IN (?)' => $fieldGroupIds]
                );
            }
        }

        return $this;
    }

    /**
     * Update status by ids
     *
     * @param [] $id
     * @param int $status
     * @return int
     */
    public function updateStatus($ids, $status)
    {
        $connection = $this->getConnection();
        return $connection->update(
            $this->getMainTable(),
            [FieldGroupCategoryInterface::STATUS => $status],
            $connection->quoteInto(FieldGroupCategoryInterface::ID . ' IN (?)', $ids)
        );
    }
}
