<?php

namespace Ewave\ProductCalculator\Model\ResourceModel;

use Ewave\ProductCalculator\Api\Data\CalculatorInterface;
use Ewave\ProductCalculator\Api\Data\FieldGroupInterface;
use Ewave\ProductCalculator\Api\Data\CalculatorFieldGroupCategoryInterface;
use Ewave\ProductCalculator\Api\Data\FieldInterface;
use Ewave\ProductCalculator\Api\Data\FieldGroupFieldInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Field extends AbstractDb
{
    const FIELD_TABLE = 'ewave_productcalculator_field';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::FIELD_TABLE, FieldInterface::ID);
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
            [FieldInterface::STATUS => $status],
            $connection->quoteInto(FieldInterface::ID . ' IN (?)', $ids)
        );
    }

    /**
     * Get input field calculator Id
     * @param int $id
     *
     * @return int|null
     */
    public function getCalculatorId($id)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(['main_table' => $this->getMainTable()], [])->join(
            ['field_group_field' => $this->getTable(FieldGroup::FIELD_GROUP_FIELD_TABLE)],
            'main_table.' . FieldInterface::ID . ' = field_group_field.' . FieldGroupFieldInterface::FIELD_ID,
            []
        )->join(
            ['field_group' => $this->getTable(FieldGroup::FIELD_GROUP_TABLE)],
            'field_group.' . FieldGroupInterface::ID . ' = field_group_field.'
            . FieldGroupFieldInterface::FIELD_GROUP_ID,
            []
        )->join(
            ['calculator_category' => $this->getTable(Calculator::CALCULATOR_FIELD_GROUP_CATEGORY_TABLE)],
            'calculator_category.' . CalculatorFieldGroupCategoryInterface::FIELD_GROUP_CATEGORY_ID . ' = field_group.'
            . FieldGroupInterface::CATEGORY_ID,
            ['calculator_category.' . CalculatorFieldGroupCategoryInterface::CALCULATOR_ID]
        )->where('main_table.' . FieldInterface::ID . ' = ?', $id);
        $calculatorId = $connection->fetchOne($select);

        return $calculatorId;
    }

    /**
     * implode array to string
     * {@inheritdoc}
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $categories = $object->getCategoriesToShow();
        $object->setCategoriesToShow(implode(',', $categories ?: []));
        return parent::_beforeSave($object);
    }

    /**
     * explode string to array
     * {@inheritdoc}
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        $categories = $object->getCategoriesToShow();
        $object->setCategoriesToShow(explode(',', $categories ?: ''));
        return parent::_afterLoad($object);
    }
}
