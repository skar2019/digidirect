<?php

namespace Digidirect\AbstractAttributesNavigation\Model\ResourceModel\Menu;

use Digidirect\Navigation\Model\ResourceModel\Menu\Collection as MenuCollection;
use Digidirect\Navigation\Model\ResourceModel\Menu\JoinTypeInterface;

/**
 * Class AbstractAttribute
 * @package Digidirect\AbstractAttributesNavigation\Model\ResourceModel\Menu
 */
class AbstractAttribute implements JoinTypeInterface
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
     * @param MenuCollection $collection
     * @param string $alias
     * @param int $storeId
     * @return MenuCollection
     */
    public function joinType(MenuCollection $collection, $alias, $storeId)
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