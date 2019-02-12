<?php

namespace Ewave\ProductCalculator\Setup;

use Ewave\ProductCalculator\Api\Data\FieldGroupInterface;
use Ewave\ProductCalculator\Api\Data\FieldInterface;
use Ewave\ProductCalculator\Model\ResourceModel\Field;
use Ewave\ProductCalculator\Model\ResourceModel\FieldGroup;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\DB\Ddl\Table;
use Ewave\ProductCalculator\Model\ResourceModel\Calculator;
use Ewave\ProductCalculator\Api\Data\CalculatorInterface;

/**
 * Class InstallSchema
 * @package Ewave\ProductCalculator\Setup
 *
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $this->createCalculatorTable($installer)
            ->createFieldGroupTable($installer)
            ->createCalculatorFieldGroupTable($installer)
            ->createFieldTable($installer)
            ->createCalculatorFieldGroupFieldTable($installer)
        ;

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    public function createCalculatorTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable(Calculator::CALCULATOR_TABLE)
        )->addColumn(
            CalculatorInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Calculator Id'
        )->addColumn(
            CalculatorInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )->addColumn(
            CalculatorInterface::BUTTON_LABEL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Button label'
        )->addColumn(
            CalculatorInterface::STATUS,
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Status'
        )->addColumn(
            CalculatorInterface::PRIORITY,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Priority'
        )->addColumn(
            CalculatorInterface::CONDITIONS_SERIALIZED,
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => false],
            'Conditions Serialized'
        )->setComment(
            'Ewave Product Calculator'
        );
        $setup->getConnection()->createTable($table);
        return $this;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    public function createFieldGroupTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable(FieldGroup::FIELD_GROUP_TABLE)
        )->addColumn(
            FieldGroupInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Field Group Id'
        )->addColumn(
            FieldGroupInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )->addColumn(
            FieldGroupInterface::STATUS,
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Status'
        )->addColumn(
            FieldGroupInterface::PRIORITY,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Priority'
        )->addColumn(
            FieldGroupInterface::LABEL,
            Table::TYPE_TEXT,
            255,
            [],
            'Label'
        )->addColumn(
            FieldGroupInterface::IS_MANDATORY,
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Is mandatory'
        )->addColumn(
            FieldGroupInterface::MANDATORY_USER_NOTICE,
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => false],
            'Mandatory user notice'
        )->addColumn(
            FieldGroupInterface::TYPE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Type'
        )->setComment(
            'Ewave Product Calculator Field Group'
        );
        $setup->getConnection()->createTable($table);
        return $this;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    public function createCalculatorFieldGroupTable(SchemaSetupInterface $setup)
    {
        $fieldGroupTable = $setup->getTable(FieldGroup::FIELD_GROUP_TABLE);
        $calculatorFieldGroupTable = $setup->getTable('ewave_productcalculator_calculator_field_group');

        $table = $setup->getConnection()->newTable(
            $calculatorFieldGroupTable
        )->addColumn(
            'calculator_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Product Calculator Id'
        )->addColumn(
            'field_group_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Field Group Id'
        )->addForeignKey(
            $setup->getFkName(
                $calculatorFieldGroupTable,
                'calculator_id',
                Calculator::CALCULATOR_TABLE,
                CalculatorInterface::ID
            ),
            'calculator_id',
            $setup->getTable(Calculator::CALCULATOR_TABLE),
            CalculatorInterface::ID,
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                $calculatorFieldGroupTable,
                'field_group_id',
                FieldGroup::FIELD_GROUP_TABLE,
                FieldGroupInterface::ID
            ),
            'field_group_id',
            $fieldGroupTable,
            FieldGroupInterface::ID,
            Table::ACTION_CASCADE
        )->setComment(
            'Ewave Product Calculators To Field Groups Relations'
        );
        $setup->getConnection()->createTable($table);
        return $this;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    public function createFieldTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable(Field::FIELD_TABLE)
        )->addColumn(
            FieldInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Field Id'
        )->addColumn(
            FieldInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )->addColumn(
            FieldInterface::STATUS,
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Status'
        )->addColumn(
            FieldInterface::PRIORITY,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Priority'
        )->addColumn(
            FieldInterface::LABEL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Label'
        )->addColumn(
            CalculatorInterface::CONDITIONS_SERIALIZED,
            Table::TYPE_TEXT,
            '64K',
            ['nullable' => false],
            'Conditions Serialized'
        )->setComment(
            'Ewave Product Calculator Field'
        );
        $setup->getConnection()->createTable($table);
        return $this;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    public function createCalculatorFieldGroupFieldTable(SchemaSetupInterface $setup)
    {
        $fieldTable = $setup->getTable(Field::FIELD_TABLE);
        $fieldGroupFieldTable = $setup->getTable(FieldGroup::FIELD_GROUP_FIELD_TABLE);

        $table = $setup->getConnection()->newTable(
            $fieldGroupFieldTable
        )->addColumn(
            'field_group_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Field Group Id'
        )->addColumn(
            'field_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Field Id'
        )->addForeignKey(
            $setup->getFkName(
                $fieldGroupFieldTable,
                'field_group_id',
                FieldGroup::FIELD_GROUP_TABLE,
                FieldGroupInterface::ID
            ),
            'field_group_id',
            $setup->getTable(FieldGroup::FIELD_GROUP_TABLE),
            FieldGroupInterface::ID,
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                $fieldGroupFieldTable,
                'field_id',
                Field::FIELD_TABLE,
                FieldInterface::ID
            ),
            'field_id',
            $fieldTable,
            FieldInterface::ID,
            Table::ACTION_CASCADE
        )->setComment(
            'Ewave Product Calculator Field Groups To Fields Relations'
        );
        $setup->getConnection()->createTable($table);
        return $this;
    }
}
