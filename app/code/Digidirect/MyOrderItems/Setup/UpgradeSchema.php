<?php

namespace Digidirect\MyOrderItems\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addDateOfPurchaseColumn($setup);
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    protected function addDateOfPurchaseColumn(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getTable('digidirect_sales_order_item_state');

        $installer->getConnection()
            ->addColumn(
                $table,
                'date_of_purchase',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    'length' => null,
                    'nullable' => true,
                    'comment' => 'Date of Purchase',
                ]
            );
        $installer->endSetup();
        return $this;
    }
}
