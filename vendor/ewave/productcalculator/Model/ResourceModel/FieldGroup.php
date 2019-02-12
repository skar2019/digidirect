<?php

namespace Ewave\ProductCalculator\Model\ResourceModel;

use Ewave\ProductCalculator\Api\Data\FieldGroupFieldInterface;
use Ewave\ProductCalculator\Api\Data\FieldGroupInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FieldGroup extends AbstractDb
{
    const FIELD_GROUP_TABLE = 'ewave_productcalculator_field_group';
    const FIELD_GROUP_FIELD_TABLE = 'ewave_productcalculator_field_group_field';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::FIELD_GROUP_TABLE, FieldGroupInterface::ID);
    }

    /**
     * Save user input field relations
     *
     * @param \Ewave\ProductCalculator\Model\FieldGroup $fieldGroup
     * @return $this
     */
    public function saveRelatedFields($fieldGroup)
    {
        $fieldGroupId = $fieldGroup->getId();
        $relatedFields = $fieldGroup->getData('links/related_fields') ?? [];
        if ($fieldGroupId) {
            $connection = $this->getConnection();
            $relatedFieldIds = [];

            foreach ($relatedFields as $relatedField) {
                $relatedFieldIds[] = $relatedField['id'];
            }

            foreach ($relatedFieldIds as $relatedFieldId) {
                $connection->insertOnDuplicate(
                    $this->getTable(self::FIELD_GROUP_FIELD_TABLE),
                    [
                        FieldGroupFieldInterface::FIELD_GROUP_ID => $fieldGroupId,
                        FieldGroupFieldInterface::FIELD_ID => $relatedFieldId
                    ],
                    [FieldGroupFieldInterface::FIELD_GROUP_ID]
                );
            }

            if (empty($relatedFieldIds)) {
                $relatedFieldIds = [0];
            }

            $connection->delete(
                $this->getTable(self::FIELD_GROUP_FIELD_TABLE),
                [
                    FieldGroupFieldInterface::FIELD_GROUP_ID . ' = ?' => $fieldGroupId,
                    FieldGroupFieldInterface::FIELD_ID . ' NOT IN (?)' => $relatedFieldIds
                ]
            );
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
            [FieldGroupInterface::STATUS => $status],
            $connection->quoteInto(FieldGroupInterface::ID . ' IN (?)', $ids)
        );
    }
}
