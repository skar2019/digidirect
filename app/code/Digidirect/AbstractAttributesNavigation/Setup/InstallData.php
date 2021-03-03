<?php

namespace Digidirect\AbstractAttributesNavigation\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 *
 * @package Digidirect\AbstractAttributesNavigation\Setup
 */
class InstallData implements InstallDataInterface
{
    const ABSTRACT_ATTRIBUTE_MENU_ITEM_TYPE = 'abstract_attribute';

    /**
     * Install data
     *
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

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
        $table = $setup->getTable('digidirect_navigation_menu_item_type');

        $setup->getConnection()->delete(
            $table,
            $setup->getConnection()->quoteInto('menu_type_code = ?', self::ABSTRACT_ATTRIBUTE_MENU_ITEM_TYPE)
        );

        $setup->getConnection()->insertArray(
            $table,
            ['type_id', 'menu_type_code', 'type_name'],
            [
                [4, 'abstract_attribute', 'Attribute'],
            ]
        );
    }
}
