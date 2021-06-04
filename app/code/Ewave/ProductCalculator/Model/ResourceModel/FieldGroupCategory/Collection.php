<?php

namespace Ewave\ProductCalculator\Model\ResourceModel\FieldGroupCategory;

use Ewave\ProductCalculator\Api\Data\CalculatorFieldGroupCategoryInterface;
use Ewave\ProductCalculator\Api\Data\FieldGroupCategoryInterface;
use Ewave\ProductCalculator\Model\FieldGroupCategory;
use Ewave\ProductCalculator\Model\ResourceModel\Calculator;
use Ewave\ProductCalculator\Model\ResourceModel\FieldGroupCategory as FieldGroupCategoryResource;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 * @package Ewave\ProductCalculator\Model\ResourceModel\FieldGroupCategory
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(FieldGroupCategory::class, FieldGroupCategoryResource::class);
    }

    /**
     * Get array of related field group categories
     *
     * @param int $calculatorId
     * @return \Magento\Framework\DataObject[]
     */
    public function getCalculatorFieldGroupCategories($calculatorId)
    {
        $this->getSelect()->join(
            ['calculator_field_group_category' => $this->getTable(Calculator::CALCULATOR_FIELD_GROUP_CATEGORY_TABLE)],
            'main_table.id = calculator_field_group_category.'
            . CalculatorFieldGroupCategoryInterface::FIELD_GROUP_CATEGORY_ID
        )
            ->where(
                'calculator_field_group_category.' . CalculatorFieldGroupCategoryInterface::CALCULATOR_ID . ' = ?',
                $calculatorId
            )
            ->order('main_table.' . FieldGroupCategoryInterface::PRIORITY . ' ' . Select::SQL_ASC);

        return $this->getItems();
    }
}
