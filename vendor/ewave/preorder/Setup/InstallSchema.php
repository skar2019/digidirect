<?php

namespace Ewave\PreOrder\Setup;

use Ewave\PreOrder\Model\ResourceModel\OrderItemPreorder;
use Ewave\PreOrder\Model\ResourceModel\OrderPreorder;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 *
 * @package Ewave\PreOrder\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->installOrderPreorderTable($installer);

        $this->installOrderItemsPreorderTable($installer);

        $installer->endSetup();
    }

    /**
     * Install OrderPreorder::TABLE
     *
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function installOrderPreorderTable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(OrderPreorder::TABLE);
        if ($installer->tableExists($tableName)) {
            return;
        }

        $table = $installer->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'is_preorder',
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'default' => '0']
            )
            ->addColumn(
                'warning',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null]
            )
            ->addForeignKey(
                $installer->getFkName(
                    OrderPreorder::TABLE,
                    'order_id',
                    'sales_order',
                    'entity_id'
                ),
                'order_id',
                $installer->getTable('sales_order'),
                'entity_id',
                Table::ACTION_CASCADE
            );

        $installer->getConnection()->createTable($table);
    }

    /**
     * Install OrderPreorder::TABLE
     *
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function installOrderItemsPreorderTable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(OrderItemPreorder::TABLE);
        if ($installer->tableExists($tableName)) {
            return;
        }

        $table = $installer->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
            )
            ->addColumn(
                'order_item_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false]
            )
            ->addColumn(
                'is_preorder',
                Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'default' => '0']
            )
            ->addForeignKey(
                $installer->getFkName(
                    OrderItemPreorder::TABLE,
                    'order_item_id',
                    'sales_order_item',
                    'item_id'
                ),
                'order_item_id',
                $installer->getTable('sales_order_item'),
                'item_id',
                Table::ACTION_CASCADE
            );

        $installer->getConnection()->createTable($table);
    }
}
