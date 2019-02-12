<?php
namespace Ewave\AdvancedInventory\Setup;

use Ewave\AdvancedInventory\Api\Data\AdvancedInventoryStockInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
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
         * Create table 'ewave_advancedinventory_stock'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('ewave_advancedinventory_stock'))
            ->addColumn(
                AdvancedInventoryStockInterface::STOCK_ID,
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Stock ID'
            )
            ->addColumn(
                AdvancedInventoryStockInterface::ABSTRACT_ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Abstract Entity Id'
            )
            ->addIndex(
                $installer->getIdxName(
                    'ewave_advancedinventory_stock',
                    [AdvancedInventoryStockInterface::STOCK_ID, AdvancedInventoryStockInterface::ABSTRACT_ENTITY_ID],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [AdvancedInventoryStockInterface::STOCK_ID, AdvancedInventoryStockInterface::ABSTRACT_ENTITY_ID],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->addForeignKey(
                $installer->getFkName(
                    'ewave_advancedinventory_stock',
                    AdvancedInventoryStockInterface::STOCK_ID,
                    'cataloginventory_stock',
                    'stock_id'
                ),
                AdvancedInventoryStockInterface::STOCK_ID,
                $installer->getTable('cataloginventory_stock'),
                'stock_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'ewave_advancedinventory_stock',
                    AdvancedInventoryStockInterface::ABSTRACT_ENTITY_ID,
                    'ewave_abstractentity_entity',
                    'entity_id'
                ),
                AdvancedInventoryStockInterface::ABSTRACT_ENTITY_ID,
                $installer->getTable('ewave_abstractentity_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Advanced Inventory Stock Table');

        $installer->getConnection()->createTable($table);
    }
}
