<?php
namespace Ewave\Faq\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Class UpgradeSchema
 *
 * @package Ewave\Faq\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addMetaTitleField($setup);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->addIdentifierField($setup);
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->addCustomerEmailField($setup);
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $this->addCategoryDesignFields($setup);
        }
        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function addCategoryDesignFields(SchemaSetupInterface $setup)
    {
        $tableName = 'ewave_faq_category';

        // Add "Layout Update" column
        $columnName = 'layout_update';
        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            $columnName,
            [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'default'  => '',
                'size'     => 255,
                'comment'  => 'Layout Update',
            ]
        );

        // Add "Layout Update XML" column
        $columnName = 'layout_update_xml';
        $setup->getConnection()->addColumn(
            $setup->getTable($tableName),
            $columnName,
            [
                'type'     => Table::TYPE_TEXT,
                'nullable' => true,
                'default'  => '',
                'comment'  => 'Layout Update XML',
            ]
        );
    }

    /**
     * Add meta title
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    protected function addMetaTitleField(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->dropColumn('ewave_faq', 'metakeyword');
        $setup->getConnection()->addColumn(
            $setup->getTable('ewave_faq'),
            'metatitle',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Faq Meta Title',
            ]
        );
        return $this;
    }

    /**
     * Add identifier
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    protected function addIdentifierField(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('ewave_faq_category'),
            'identifier',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Faq Category Identifier',
            ]
        );
        return $this;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    protected function addCustomerEmailField(SchemaSetupInterface $setup)
    {
        $table = 'ewave_faq';
        $columnName = 'customer_email';
        if (!$setup->getConnection()->tableColumnExists($table, $columnName)) {
            $setup->getConnection()->addColumn(
                $setup->getTable($table),
                $columnName,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Customer Email',
                ]
            );
        }

        $columnName = 'from_store';
        if (!$setup->getConnection()->tableColumnExists($table, $columnName)) {
            $setup->getConnection()->addColumn(
                $setup->getTable($table),
                $columnName,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 5,
                    'nullable' => true,
                    'comment' => 'Store ID',
                ]
            );
        }
        return $this;
    }
}
