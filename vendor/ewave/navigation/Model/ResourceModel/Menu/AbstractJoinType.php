<?php

namespace Ewave\Navigation\Model\ResourceModel\Menu;

/**
 * Class AbstractJoinType
 *
 * @package Ewave\Navigation\Model\ResourceModel\Menu
 */
class AbstractJoinType implements JoinTypeInterface
{
    /**
     * @var []
     */
    protected $data;

    /**
     * Category constructor.
     *
     * @param [] $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param Collection $collection
     * @param string $alias
     * @param int $storeId
     * @return Collection
     */
    public function joinType(Collection $collection, $alias, $storeId)
    {
        $tableAlias = $alias . $this->getTableAlias();
        $fieldsToSelect = [];
        foreach ($this->getFields() as $field) {
            $fieldsToSelect[] = $tableAlias . '.' . $field;
        }
        $collection->getSelect()->joinLeft(
            [$tableAlias => $collection->getTable($this->getTable())],
            'main_table.entity_id = ' . $tableAlias . '.menu_item_id AND ' . $tableAlias . '.store_id =' . $storeId,
            $fieldsToSelect
        );

        return $collection;
    }

    /**
     * @return []
     */
    public function getFields()
    {
        return $this->data['fields'];
    }

    /**
     * @return string
     */
    public function getTableAlias()
    {
        return $this->data['alias'];
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->data['table_name'];
    }

    /**
     * @return string
     */
    public function getFieldPrefix()
    {
        return $this->data['field_prefix'];
    }
}
