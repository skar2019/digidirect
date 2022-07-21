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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Upgrade the schema version from old to new
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->addIsMageSparkField($setup);
        $this->addExpireField($setup);
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
            Table::TYPE_SMALLINT,
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
            Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Expiration Date'
        );
    }
}
