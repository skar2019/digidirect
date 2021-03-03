<?php

//@codingStandardsIgnoreFile

namespace Digidirect\AbstractAttributesNavigation\Setup;

use Magento\Framework\Setup\{
    InstallSchemaInterface,
    ModuleContextInterface,
    SchemaSetupInterface
};
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Trigger;

/**
 * Class InstallSchema
 * @package Digidirect\Navigation\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    const DIGIDIRECT_NAVIGATION_MENU_ABSTRACT_ATTRIBUTE_TYPE = 'digidirect_navigation_menu_abstract_attribute_type';
    const DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE = 'digidirect_navigation_menu_entity';

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create custom link type menu item table
         */
        $this->createAbstractAttributeType($installer);


        $installer->endSetup();
    }


    /**
     * Create item info table
     *
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function createAbstractAttributeType(SchemaSetupInterface $installer)
    {
        $tableAttributes = $installer->getConnection()
            ->newTable($installer->getTable(self::DIGIDIRECT_NAVIGATION_MENU_ABSTRACT_ATTRIBUTE_TYPE))
            ->addColumn(
                'menu_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Entity Id'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Store Id'
            )->addColumn(
                'option_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [
                    'nullable' => true
                ],
                'Navigation Item Title'
            )->addColumn(
                'attribute_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                50,
                [
                    'nullable' => true
                ],
                'Abstract Attribute ID'
            )/*->addForeignKey(
                $installer->getFkName(
                    self::DIGIDIRECT_NAVIGATION_MENU_ABSTRACT_ATTRIBUTE_TYPE,
                    'attribute_id',
                    $installer->getTable('digidirect_aa'),
                    'attribute_id'
                ),
                'attribute_id',
                $installer->getTable('digidirect_aa'),
                'attribute_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            */->addForeignKey(
                $installer->getFkName(
                    self::DIGIDIRECT_NAVIGATION_MENU_ABSTRACT_ATTRIBUTE_TYPE,
                    'menu_item_id',
                    self::DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE,
                    'entity_id'
                ),
                'menu_item_id',
                $installer->getTable(self::DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    self::DIGIDIRECT_NAVIGATION_MENU_ABSTRACT_ATTRIBUTE_TYPE,
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Digidirect Navigation Abstract Attributes Type'
            );
        $installer->getConnection()->createTable($tableAttributes);
    }
}
