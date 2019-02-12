<?php

namespace Ewave\ProductCalculator\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;
use Ewave\ProductCalculator\Model\ResourceModel\Calculator;
use Ewave\ProductCalculator\Model\ResourceModel\FieldGroup;
use Ewave\ProductCalculator\Model\ResourceModel\FieldGroupCategory;
use Ewave\ProductCalculator\Model\ResourceModel\Field;
use Ewave\ProductCalculator\Api\Data\CalculatorInterface;
use Ewave\ProductCalculator\Api\Data\CalculatorFieldGroupCategoryInterface;
use Ewave\ProductCalculator\Api\Data\FieldGroupInterface;
use Ewave\ProductCalculator\Api\Data\FieldGroupCategoryInterface;
use Ewave\ProductCalculator\Api\Data\FieldInterface;
use Ewave\ProductCalculator\Model\Source\Status;
use Ewave\ProductCalculator\Model\Source\ResultLoadActions;

/**
 * Class UpgradeSchema
 * @package Ewave\ProductCalculator\Setup
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
            $this->addUpdateCalculatorTable($setup);
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->addCalculatorColumns($setup);
            $this->createFieldGroupCategoryTable($setup);
            $this->addAdditionalAttributes($setup);
            $this->createCalculatorFieldGroupCategoryTable($setup);
            $this->createCalculatorFieldGroupCategoryTable($setup);
            $this->migrateDataToNewStructure($setup);
            $this->dropCalculatorFieldGroupTable($setup);
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->addProductsCountForCategoryField($setup);
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $this->addSeparateProductsByCategories($setup);
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable(Calculator::CALCULATOR_TABLE),
                'categories_to_show',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Categories To Show',
                    'after' => CalculatorInterface::BUTTON_LABEL
                ]
            );
        }

        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $setup->getConnection()->dropColumn(Calculator::CALCULATOR_TABLE, 'categories_to_show');
            $setup->getConnection()->addColumn(
                $setup->getTable(Field::FIELD_TABLE),
                FieldInterface::CATEGORIES_TO_SHOW,
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 512,
                    'nullable' => true,
                    'comment' => 'Categories To Show',
                    'after' => FieldInterface::CONDITIONS_SERIALIZED
                ]
            );
        }

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function addUpdateCalculatorTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(Calculator::CALCULATOR_TABLE),
            CalculatorInterface::PRODUCTS_COUNT,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'comment' => 'Products count',
                'after' => CalculatorInterface::BUTTON_LABEL
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function addCalculatorColumns(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(Calculator::CALCULATOR_TABLE),
            CalculatorInterface::RESULT_LOAD_ACTION,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => ResultLoadActions::LOAD,
                'comment' => 'Result Load Actions',
                'after' => CalculatorInterface::CONDITIONS_SERIALIZED
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable(Calculator::CALCULATOR_TABLE),
            CalculatorInterface::RESULT_LOAD_URL,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Result Load Url',
                'after' => CalculatorInterface::RESULT_LOAD_ACTION
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable(Calculator::CALCULATOR_TABLE),
            CalculatorInterface::FLAG_CUSTOM_LOAD_URL,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'comment' => 'Use Custom Load Url',
                'after' => CalculatorInterface::RESULT_LOAD_URL
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function addAdditionalAttributes(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(FieldGroup::FIELD_GROUP_TABLE),
            FieldGroupInterface::DESCRIPTION,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 512,
                'nullable' => true,
                'comment' => 'Description',
                'after' => FieldGroupInterface::NAME
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable(FieldGroup::FIELD_GROUP_TABLE),
            FieldGroupInterface::CSS_CLASS,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 35,
                'nullable' => true,
                'comment' => 'Css Class',
                'after' => FieldGroupInterface::TYPE
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable(FieldGroup::FIELD_GROUP_TABLE),
            FieldGroupInterface::CATEGORY_ID,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Category ID',
                'after' => FieldGroupInterface::CSS_CLASS
            ]
        );
        $setup->getConnection()->addForeignKey(
            $setup->getFkName(
                FieldGroup::FIELD_GROUP_TABLE,
                FieldGroupInterface::CATEGORY_ID,
                FieldGroupCategory::FIELD_GROUP_CATEGORY_TABLE,
                FieldGroupCategoryInterface::ID
            ),
            FieldGroup::FIELD_GROUP_TABLE,
            FieldGroupInterface::CATEGORY_ID,
            FieldGroupCategory::FIELD_GROUP_CATEGORY_TABLE,
            FieldGroupCategoryInterface::ID,
            Table::ACTION_SET_NULL
        );
        $setup->getConnection()->addColumn(
            $setup->getTable(FieldGroup::FIELD_GROUP_TABLE),
            FieldGroupInterface::CUSTOM_INPUT_NAME,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 64,
                'nullable' => true,
                'comment' => 'Custom Input Name',
                'after' => FieldGroupInterface::CATEGORY_ID
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable(FieldGroup::FIELD_GROUP_TABLE),
            FieldGroupInterface::VALUE_FIELD,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 16,
                'nullable' => false,
                'default' => FieldGroupInterface::ID,
                'comment' => 'Value Field',
                'after' => FieldGroupInterface::CUSTOM_INPUT_NAME
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable(Field::FIELD_TABLE),
            FieldInterface::CSS_CLASS,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 35,
                'nullable' => true,
                'comment' => 'Css Class',
                'after' => FieldInterface::CONDITIONS_SERIALIZED
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable(Field::FIELD_TABLE),
            FieldInterface::IMAGE,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Image',
                'after' => FieldInterface::CSS_CLASS
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function createFieldGroupCategoryTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable(FieldGroupCategory::FIELD_GROUP_CATEGORY_TABLE)
        )->addColumn(
            FieldGroupCategoryInterface::ID,
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Field Id'
        )->addColumn(
            FieldGroupCategoryInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Name'
        )->addColumn(
            FieldGroupCategoryInterface::DESCRIPTION,
            Table::TYPE_TEXT,
            512,
            ['nullable' => true],
            'Description'
        )->addColumn(
            FieldGroupCategoryInterface::STATUS,
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => Status::ACTIVE],
            'Status'
        )->addColumn(
            FieldGroupCategoryInterface::PRIORITY,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Priority'
        )->addColumn(
            FieldGroupCategoryInterface::LABEL,
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Label'
        )->setComment(
            'Ewave Product Calculator Field Group Category'
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    public function createCalculatorFieldGroupCategoryTable(SchemaSetupInterface $setup)
    {
        $fieldGroupCategoryTable = $setup->getTable(FieldGroupCategory::FIELD_GROUP_CATEGORY_TABLE);
        $calculatorFieldGroupCategoryTable = $setup->getTable(Calculator::CALCULATOR_FIELD_GROUP_CATEGORY_TABLE);

        $table = $setup->getConnection()->newTable(
            $calculatorFieldGroupCategoryTable
        )->addColumn(
            CalculatorFieldGroupCategoryInterface::CALCULATOR_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Product Calculator Id'
        )->addColumn(
            CalculatorFieldGroupCategoryInterface::FIELD_GROUP_CATEGORY_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Field Group Category Id'
        )->addForeignKey(
            $setup->getFkName(
                $calculatorFieldGroupCategoryTable,
                CalculatorFieldGroupCategoryInterface::CALCULATOR_ID,
                Calculator::CALCULATOR_TABLE,
                CalculatorInterface::ID
            ),
            CalculatorFieldGroupCategoryInterface::CALCULATOR_ID,
            $setup->getTable(Calculator::CALCULATOR_TABLE),
            CalculatorInterface::ID,
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                $calculatorFieldGroupCategoryTable,
                CalculatorFieldGroupCategoryInterface::FIELD_GROUP_CATEGORY_ID,
                FieldGroup::FIELD_GROUP_TABLE,
                FieldGroupInterface::ID
            ),
            CalculatorFieldGroupCategoryInterface::FIELD_GROUP_CATEGORY_ID,
            $fieldGroupCategoryTable,
            FieldGroupInterface::ID,
            Table::ACTION_CASCADE
        )->setComment(
            'Ewave Product Calculators To Field Groups Category Relations'
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    public function migrateDataToNewStructure(SchemaSetupInterface $setup)
    {
        $groupsNumber = $setup->getConnection()->fetchOne(
            $setup->getConnection()->select()->from(
                $setup->getTable(FieldGroup::FIELD_GROUP_TABLE),
                new \Zend_Db_Expr('COUNT(*)')
            )
        );
        if ($groupsNumber > 0) {

            $calculatorFieldGroups = $setup->getConnection()->fetchAll(
                $setup->getConnection()->select()->from(
                    $setup->getTable('ewave_productcalculator_calculator_field_group')
                )
            );

            $calculators = $setup->getConnection()->fetchPairs(
                $setup->getConnection()->select()->from(
                    $setup->getTable(Calculator::CALCULATOR_TABLE),
                    [CalculatorInterface::ID, CalculatorInterface::NAME]
                )
            );

            foreach ($calculators as $calculatorId => $name) {
                // create default category for calculator
                $setup->getConnection()->insert(
                    $setup->getTable(FieldGroupCategory::FIELD_GROUP_CATEGORY_TABLE),
                    ['name' => __('Default %1', $name)]
                );
                $defaultCategoryId = $setup->getConnection()->lastInsertId(
                    $setup->getTable(FieldGroupCategory::FIELD_GROUP_CATEGORY_TABLE)
                );

                //attach category to calculator
                $setup->getConnection()->insert(
                    $setup->getTable(Calculator::CALCULATOR_FIELD_GROUP_CATEGORY_TABLE),
                    [
                        CalculatorFieldGroupCategoryInterface::CALCULATOR_ID           => $calculatorId,
                        CalculatorFieldGroupCategoryInterface::FIELD_GROUP_CATEGORY_ID => $defaultCategoryId
                    ]
                );
                //attach field groups to category
                $fieldGroupIds = [];
                foreach ($calculatorFieldGroups as $calculatorFieldGroup) {
                    if ($calculatorFieldGroup['calculator_id'] == $calculatorId) {
                        $fieldGroupIds[] = $calculatorFieldGroup['field_group_id'];
                    }
                }

                if (!empty($fieldGroupIds)) {
                    $setup->getConnection()->update(
                        $setup->getTable(FieldGroup::FIELD_GROUP_TABLE),
                        [FieldGroupInterface::CATEGORY_ID => $defaultCategoryId],
                        [FieldGroupInterface::ID . ' in (?)' => $fieldGroupIds]
                    );
                }
            }
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    public function dropCalculatorFieldGroupTable(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->truncateTable(
            $setup->getTable('ewave_productcalculator_calculator_field_group')
        );
        $setup->getConnection()->dropTable(
            $setup->getTable('ewave_productcalculator_calculator_field_group')
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function addProductsCountForCategoryField(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(Calculator::CALCULATOR_TABLE),
            CalculatorInterface::PRODUCTS_COUNT_FOR_CATEGORY,
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'comment' => 'Products count for category',
                'after' => CalculatorInterface::BUTTON_LABEL
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function addSeparateProductsByCategories(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(Calculator::CALCULATOR_TABLE),
            'separate_products_by_categories',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => false,
                'comment' => 'Separate Products By Categories',
                'after' => CalculatorInterface::BUTTON_LABEL
            ]
        );
    }
}
