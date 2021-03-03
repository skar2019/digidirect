<?php
namespace Digidirect\Navigation\Model\ResourceModel\Type;

/**
 * Interface SaveProcessorInterface
 * @package Digidirect\Navigation\Model\ResourceModel\Type
 */
interface SaveProcessorInterface
{
    /**
     * @param \Digidirect\Navigation\Model\Menu $menuItem
     * @return \Digidirect\Navigation\Model\Menu
     */
    public function saveInformation(\Digidirect\Navigation\Model\Menu $menuItem);

    /**
     * @param \Magento\Framework\DB\Select $select
     * @param string $mainTable
     * @param int $storeId
     * @return \Magento\Framework\DB\Select
     */
    public function getJoinRule(\Magento\Framework\DB\Select $select, string $mainTable, $storeId);
}
