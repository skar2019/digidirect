<?php
/**
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  MageSpark
 * @package   MageSpark\Base
 * @author    MageSpark team <support@magespark.com>
 * @copyright 2020 MageSpark
 */


namespace MageSpark\Base\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrade the schema version from old to new
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            $this->addIsMageSparkField($setup);
        }

        if (version_compare($context->getVersion(), '1.4.2', '<')) {
            $this->addExpireField($setup);
        }
    }

    /**
     * Add the megasparknotofication column
     * @param SchemaSetupInterface $setup
     */
    private function addIsMageSparkField(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('adminnotification_inbox'),
            'is_magespark',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Is MageSpark Notification'
        );
    }

    /**
     * Add the expiration date column
     * @param SchemaSetupInterface $setup
     */
    private function addExpireField(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('adminnotification_inbox'),
            'expiration_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Expiration Date'
        );
    }
}
