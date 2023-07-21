<?php
namespace Digidirect\CollaborateForm\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;  
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        /**
         * Create table 'digidirect_collaborate_form'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('zeb_collaborate_form')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'firstname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'FirstName'
        )->addColumn(
            'lastname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'FirstName'
        )->addColumn(
            'email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Email'
        )->addColumn(
            'socialmedia_link',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Social Media'
        )->addColumn(
            'notes',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            500,
            ['nullable' => false],
            'Tell us about your idea'
        );
        
        $installer->getConnection()->createTable($table);
    }
}