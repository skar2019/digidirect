<?php
namespace Digidirect\Faq\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Digidirect\Faq\Setup
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

        $this->_createFaqCategory($installer);
        $this->_createFaq($installer);
        $this->_createFaqCategoryRelation($installer);
        $this->_createFaqCategoryStore($installer);
        $this->_createFaqTag($installer);
        $this->_createFagTagRelation($installer);

        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    protected function _createFagTagRelation(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('digidirect_faq_tag_relation')
        )->addColumn(
            'faq_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'faq_id'
        )->addColumn(
            'tag_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'tag_id'
        )->addIndex(
            $installer->getIdxName('digidirect_faq_tag_relation', ['faq_id']),
            ['faq_id']
        )->addIndex(
            $installer->getIdxName('digidirect_faq_tag_relation', ['tag_id']),
            ['tag_id']
        )->addForeignKey(
            $installer->getFkName('digidirect_faq_tag_relation', 'faq_id', 'digidirect_faq', 'entity_id'),
            'faq_id',
            $installer->getTable('digidirect_faq'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('digidirect_faq_tag_relation', 'tag_id', 'digidirect_faq_tag', 'entity_id'),
            'tag_id',
            $installer->getTable('digidirect_faq_tag'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->getConnection()->createTable($table);
        return $this;
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    protected function _createFaqTag(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('digidirect_faq_tag')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'entity_id'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false, 'default' => ''],
            'title'
        );

        $installer->getConnection()->createTable($table);
        return $this;
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    protected function _createFaqCategoryStore(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('digidirect_faq_category_store')
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'category_id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => false],
            'Store ID'
        )->addIndex(
            $installer->getIdxName(
                'digidirect_faq_category_store',
                ['category_id', 'store_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['category_id', 'store_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('digidirect_faq_category_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('digidirect_faq_category_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('digidirect_faq_category_store', 'category_id', 'digidirect_faq_category', 'entity_id'),
            'category_id',
            $installer->getTable('digidirect_faq_category'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->getConnection()->createTable($table);
        return $this;
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    protected function _createFaqCategoryRelation(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('digidirect_faq_category_relation')
        )->addColumn(
            'faq_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'faq_id'
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true],
            'category_id'
        )->addIndex(
            $installer->getIdxName('digidirect_faq_category_relation', ['faq_id']),
            ['faq_id']
        )->addIndex(
            $installer->getIdxName('digidirect_faq_category_relation', ['category_id']),
            ['category_id']
        )->addForeignKey(
            $installer->getFkName('digidirect_faq_category_relation', 'faq_id', 'digidirect_faq', 'entity_id'),
            'faq_id',
            $installer->getTable('digidirect_faq'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('digidirect_faq_category_relation', 'category_id', 'digidirect_faq_category', 'entity_id'),
            'category_id',
            $installer->getTable('digidirect_faq_category'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $installer->getConnection()->createTable($table);
        return $this;
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    protected function _createFaq(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('digidirect_faq')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'entity_id'
        )->addColumn(
            'question',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '1000',
            ['nullable' => false, 'default' => ''],
            'Question'
        )->addColumn(
            'answer',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'Answer'
        )->addColumn(
            'url_key',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false, 'default' => ''],
            'url_key'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'status'
        )->addColumn(
            'created_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'created_time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'update_time'
        )->addColumn(
            'ordering',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true, 'default' => '0'],
            'ordering'
        )->addColumn(
            'metakeyword',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'meta_keyword'
        )->addColumn(
            'metadescription',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'meta_description'
        );

        $installer->getConnection()->createTable($table);
        return $this;
    }

    /**
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    protected function _createFaqCategory(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('digidirect_faq_category')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'entity_id'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '255',
            ['nullable' => false, 'default' => ''],
            'title'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'status'
        )->addColumn(
            'ordering',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'unsigned' => true, 'default' => '0'],
            'ordering'
        );
        $installer->getConnection()->createTable($table);
        return $this;
    }
}
