<?php
namespace Ewave\Navigation\Model\ResourceModel\Type;

/**
 * Interface SaveProcessorInterface
 * @package Ewave\Navigation\Model\ResourceModel\Type
 */
interface SaveProcessorInterface
{
    /**
     * @param \Ewave\Navigation\Model\Menu $menuItem
     * @return \Ewave\Navigation\Model\Menu
     */
    public function saveInformation(\Ewave\Navigation\Model\Menu $menuItem);

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param string $mainTable
     * @param int $storeId
     * @return \Magento\Framework\DB\Select
     */
    public function getJoinRule(\Magento\Framework\DB\Select $select, string $mainTable, $storeId);
}
