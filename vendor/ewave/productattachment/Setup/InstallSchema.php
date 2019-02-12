<?php
namespace Ewave\ProductAttachment\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Ewave\ProductAttachment\Model\Attachment;

/**
 * Class InstallSchema
 * @package Ewave\ProductAttachment\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable('ewave_product_attachment')
        )->addColumn(
            Attachment::ENTITY_ID,
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'entity_id'
        )->addColumn(
            Attachment::FILE_PATH,
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false, 'default' => ''],
            'File Path'
        )->addColumn(
            Attachment::STATUS,
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'status'
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('ewave_product_attachment_attributes')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'entity_id'
        )->addColumn(
            'attachment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Attachment Id'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Attachment Name'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => false],
            'Store ID'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false, 'default' => ''],
            'Attachment Name'
        )->addIndex(
            $installer->getIdxName('ewave_product_attachment_attributes', ['store_id']),
            ['store_id']
        )->addIndex(
            $installer->getIdxName('ewave_product_attachment_attributes', ['attachment_id']),
            ['attachment_id']
        )->addForeignKey(
            $installer->getFkName('ewave_product_attachment_attributes', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'ewave_product_attachment_attributes',
                'attachment_id',
                'ewave_product_attachment',
                'entity_id'
            ),
            'attachment_id',
            $installer->getTable('ewave_product_attachment'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('ewave_product_attachment_relation')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'entity_id'
        )->addColumn(
            'attachment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Attachment Id'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'Product Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => false],
            'Store ID'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'unsigned' => true],
            'Position'
        )->addIndex(
            $installer->getIdxName('ewave_product_attachment_relation', ['attachment_id']),
            ['attachment_id']
        )->addIndex(
            $installer->getIdxName(
                'ewave_product_attachment_relation',
                ['product_id', 'attachment_id', 'store_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['product_id', 'attachment_id', 'store_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $installer->getFkName(
                'ewave_product_attachment_relation',
                'attachment_id',
                'ewave_product_attachment',
                'entity_id'
            ),
            'attachment_id',
            $installer->getTable('ewave_product_attachment'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'ewave_product_attachment_relation',
                'product_id',
                'catalog_product_entity',
                'entity_id'
            ),
            'product_id',
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('ewave_product_attachment_relation', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
