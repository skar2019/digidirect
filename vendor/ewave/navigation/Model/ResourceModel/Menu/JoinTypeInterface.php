<?php
namespace Ewave\Navigation\Model\ResourceModel\Menu;

/**
 * Interface joinTypeInterface
 *
 * @package Ewave\Navigation\Model\ResourceModel\Menu
 */
interface JoinTypeInterface
{
    /**
     * @param Collection $collection
     * @param string $alias
     * @param int $storeId
     * @return Collection
     */
    public function joinType(\Ewave\Navigation\Model\ResourceModel\Menu\Collection $collection, $alias, $storeId);

    /**
     * @return string
     */
    public function getFieldPrefix();

    /**
     * @return string
     */
    public function getTableAlias();

    /**
     * @return string
     */
    public function getTable();

    /**
     * @return []
     */
    public function getFields();
}
