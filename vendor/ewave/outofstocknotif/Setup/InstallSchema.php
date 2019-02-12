<?php
namespace Ewave\OutOfStockNotif\Setup;

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

        $productAlertStockTable = 'product_alert_stock';
        if ($setup->getConnection()->isTableExists($productAlertStockTable)) {
            $setup->getConnection()->dropForeignKey($setup->getTable($productAlertStockTable), $setup->getFkName(
                $setup->getTable($productAlertStockTable),
                'customer_id',
                $setup->getTable('customer_entity'),
                'entity_id'
            ));

            $setup->getConnection()->addColumn(
                $setup->getTable($productAlertStockTable),
                'email',
                [
                    'type'     => Table::TYPE_TEXT,
                    'nullable' => false,
                    'default'  => '',
                    'comment'  => 'Customer/Guest Email',
                ]
            );
        }

        $installer->endSetup();
    }
}
