<?php

namespace Ewave\ProductCalculator\Model\ResourceModel\Field;

use Ewave\ProductCalculator\Api\Data\FieldGroupFieldInterface;
use Ewave\ProductCalculator\Api\Data\FieldInterface;
use Ewave\ProductCalculator\Model\Field;
use Ewave\ProductCalculator\Model\ResourceModel\Field as FieldResource;
use Ewave\ProductCalculator\Model\ResourceModel\FieldGroup;
use Magento\Framework\DB\Select;

/**
 * Class Collection
 * @package Ewave\ProductCalculator\Model\ResourceModel\Field
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
        $this->_init(Field::class, FieldResource::class);
    }

    /**
     * Get array of related fields
     *
     * @param int $fieldGroupId
     * @return \Magento\Framework\DataObject[]
     */
    public function getFieldGroupRelatedFields($fieldGroupId)
    {
        $this->getSelect()->join(
            ['field_group_field' => $this->getTable(FieldGroup::FIELD_GROUP_FIELD_TABLE)],
            'main_table.id = field_group_field.' . FieldGroupFieldInterface::FIELD_ID
        )
            ->where('field_group_field.' . FieldGroupFieldInterface::FIELD_GROUP_ID . ' = ?', $fieldGroupId)
            ->order('main_table.' . FieldInterface::PRIORITY . ' ' . Select::SQL_DESC);

        return $this->getItems();
    }
}
