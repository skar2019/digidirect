<?php

namespace Digidirect\MyOrderItems\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'digidirect_sales_order_item_state'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('digidirect_sales_order_item_state'))
            ->addColumn(
                'sales_item_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Sales Item ID'
            )
            ->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Sort Order'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '1'],
                'Status'
            )->addForeignKey(
                $setup->getFkName(
                    $setup->getTable('digidirect_sales_order_item_state'),
                    'sales_item_id',
                    $setup->getTable('sales_order_item'),
                    'item_id'
                ),
                'sales_item_id',
                $setup->getTable('sales_order_item'),
                'item_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Sales order item state');

        $installer->getConnection()->createTable($table);
    }
}
