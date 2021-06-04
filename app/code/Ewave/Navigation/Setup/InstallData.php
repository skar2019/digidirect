<?php

namespace Ewave\Navigation\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @package Ewave\Navigation\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * Install data
     *
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->insertMenuSets($setup);
        $this->insertMenuTypes($setup);

        $setup->endSetup();
    }

    /**
     * Insert menu types
     *
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function insertMenuTypes(ModuleDataSetupInterface $setup)
    {
        $setup->getConnection()->insertArray(
            $setup->getTable('ewave_navigation_menu_item_type'),
            ['menu_type_code', 'type_name'],
            [
                ['cms_block', 'CMS Block'],
                ['category', 'Category'],
                ['custom_link', 'Custom Link']
            ]
        );
    }

    /**
     * Insert default sets Main Menu(instead of default magento) and footer
     *
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function insertMenuSets(ModuleDataSetupInterface $setup)
    {
        $setTable = $setup->getTable('ewave_navigation_menu_set');
        $connection = $setup->getConnection();
        $connection->insertArray(
            $setTable,
            ['set_code', 'name', 'status'],
            [
                ['main_menu', 'Main Menu', 1],
                ['footer', 'Footer', 1]
            ]
        );

        $select = $connection->select()
            ->from($setTable, ['set_id']);

        $menuSetsIds = $connection->fetchCol($select);

        $arrayToInsert = [];

        foreach ($menuSetsIds as $key => $id) {
            $arrayToInsert[$key] = [
                $id,
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            ];
        }

        $connection->delete(
            $connection->getTableName('ewave_navigation_menu_set_store')
        );

        $connection->insertArray(
            $connection->getTableName('ewave_navigation_menu_set_store'),
            ['menu_set_id', 'store_id'],
            $arrayToInsert
        );

        return $setup;
    }
}
