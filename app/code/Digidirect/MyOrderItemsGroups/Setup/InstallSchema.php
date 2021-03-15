<?php

namespace Digidirect\MyOrderItemsGroups\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupInterface;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupLinkInterface;

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
         * Create table 'digidirect_sales_order_item_group'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('digidirect_sales_order_item_group'))
            ->addColumn(
                OrderItemGroupInterface::GROUP_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Item Group ID'
            )
            ->addColumn(
                OrderItemGroupInterface::NAME,
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Group Name'
            )->addColumn(
                OrderItemGroupInterface::CUSTOMER_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer ID'
            )->addColumn(
                OrderItemGroupInterface::UPDATED_AT,
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                [],
                'Updated at'
            )->addForeignKey(
                $setup->getFkName(
                    $setup->getTable('digidirect_sales_order_item_group'),
                    'customer_id',
                    $setup->getTable('customer_entity'),
                    'entity_id'
                ),
                'customer_id',
                $setup->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Sales order item group');

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'digidirect_item_group_link'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('digidirect_order_item_group_link'))
            ->addColumn(
                OrderItemGroupLinkInterface::LINK_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Link ID'
            )
            ->addColumn(
                OrderItemGroupLinkInterface::GROUP_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Group ID'
            )->addColumn(
                OrderItemGroupLinkInterface::SALES_ITEM_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Item ID'
            )->addColumn(
                OrderItemGroupLinkInterface::POSITION,
                Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Position'
            )->addForeignKey(
                $setup->getFkName(
                    $setup->getTable('digidirect_order_item_group_link'),
                    'group_id',
                    $setup->getTable('digidirect_sales_order_item_group'),
                    'group_id'
                ),
                'group_id',
                $setup->getTable('digidirect_sales_order_item_group'),
                'group_id',
                Table::ACTION_CASCADE
            )->setComment('Order item group link');

        $installer->getConnection()->createTable($table);
    }
}
