<?php
namespace Ewave\ExtendedCartPriceRules\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @inheritdoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addPaymentMethodLimitColumn($setup);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->addEwaveExtendedCartPriceRuleTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * The 'payment_method_limit' column added to the 'salesrule' table.
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function addPaymentMethodLimitColumn(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('salesrule'),
            'payment_method_limit',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Payment Method Limit',
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return  void
     */
    protected function addEwaveExtendedCartPriceRuleTable(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('ewave_extended_cart_price_rule');
        if (!$setup->getConnection()->isTableExists($tableName)) {
            $table = $setup->getConnection()->newTable(
                $tableName
            )->addColumn(
                'rule_id',
                Table::TYPE_INTEGER,
                10,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'id'
            )->addColumn(
                'message',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'message'
            )->addForeignKey(
                $setup->getFkName(
                    $tableName,
                    'rule_id',
                    'sequence_salesrule',
                    'sequence_value'
                ),
                'rule_id',
                $setup->getTable('sequence_salesrule'),
                'sequence_value',
                Table::ACTION_CASCADE,
                Table::ACTION_CASCADE
            );
            $setup->getConnection()->createTable($table);
        }
    }
}
