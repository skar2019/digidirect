<?php

namespace Ewave\ProductCalculator\Model\ResourceModel\FieldGroup;

use Ewave\ProductCalculator\Api\Data\CalculatorFieldGroupInterface;
use Ewave\ProductCalculator\Api\Data\FieldGroupInterface;
use Ewave\ProductCalculator\Model\FieldGroup;
use Ewave\ProductCalculator\Model\ResourceModel\Calculator;
use Ewave\ProductCalculator\Model\ResourceModel\FieldGroup as FieldGroupResource;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 * @package Ewave\ProductCalculator\Model\ResourceModel\FieldGroup
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
        $this->_init(FieldGroup::class, FieldGroupResource::class);
    }

    /**
     * Get array of related field groups
     *
     * @param int $categoryId
     * @return \Magento\Framework\DataObject[]
     */
    public function getCategoryFieldGroups($categoryId)
    {
        $this->getSelect()
            ->where('main_table.' . FieldGroupInterface::CATEGORY_ID . ' = ?', $categoryId)
            ->order('main_table.' . FieldGroupInterface::PRIORITY . ' ' . Select::SQL_ASC);

        return $this->getItems();
    }
}
