<?php

namespace Ewave\Banner\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    const EWAVE_BANNER_ATTRIBUTES = 'ewave_banner_attributes';

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $this->_createBannerImagesTable($setup);
        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return SchemaSetupInterface
     */
    protected function _createBannerImagesTable(SchemaSetupInterface $setup)
    {
        $installer = $setup;
        $setup->getConnection()->dropTable($setup->getTable(self::EWAVE_BANNER_ATTRIBUTES));
        $table = $installer->getConnection()->newTable(
            $installer->getTable(self::EWAVE_BANNER_ATTRIBUTES)
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true,
            ],
            'Entity Id'
        )->addColumn(
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false,
                'unsigned' => true,
                'default' => '0',
            ],
            'banner_id'
        )->addColumn(
            'images',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [
                'nullable' => true,
            ],
            'Images serialized'
        )->addColumn(
            'navigation_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [
                'nullable' => true,
            ],
            'Navigation Title'
        )->addColumn(
            'target_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [
                'nullable' => true,
            ],
            'Target Link Type'
        )->addColumn(
            'target_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [
                'nullable' => true,
            ],
            'Target Link Value'
        )->addForeignKey(
            $installer->getFkName(
                self::EWAVE_BANNER_ATTRIBUTES,
                'banner_id',
                TableConstants::TABLE,
                'banner_id'
            ),
            'banner_id',
            $installer->getTable(TableConstants::TABLE),
            'banner_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Ewave Banner Images'
        );
        $installer->getConnection()->createTable($table);
        return $setup;
    }
}
