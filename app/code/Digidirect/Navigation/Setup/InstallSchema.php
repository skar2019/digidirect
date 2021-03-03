<?php

//@codingStandardsIgnoreFile
namespace Digidirect\Navigation\Setup;

use Magento\Framework\Setup\{
    InstallSchemaInterface,
    ModuleContextInterface,
    SchemaSetupInterface
};
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Trigger;

/**
 * Class InstallSchema
 * @package Digidirect\Navigation\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    protected $triggerFactory;

    /**
     * InstallSchema constructor.
     * @param \Magento\Framework\DB\Ddl\TriggerFactory $triggerFactory
     */
    public function __construct(\Magento\Framework\DB\Ddl\TriggerFactory $triggerFactory)
    {
        $this->triggerFactory = $triggerFactory;
    }

    /**
     * Tables names
     */
    const DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE = 'digidirect_navigation_menu_entity';
    const DIGIDIRECT_NAVIGATION_MENU_ITEM_TYPE_TABLE = 'digidirect_navigation_menu_item_type';
    const DIGIDIRECT_NAVIGATION_MENU_CUSTOM_LINK_TYPE_TABLE = 'digidirect_navigation_menu_custom_link_type';
    const DIGIDIRECT_NAVIGATION_MENU_CATEGORY_TYPE_TABLE = 'digidirect_navigation_menu_category_type';
    const DIGIDIRECT_NAVIGATION_MENU_CMS_BLOCK_TYPE_TABLE = 'digidirect_navigation_menu_cms_block_type';
    const DIGIDIRECT_NAVIGATION_MENU_SET_LINK = 'digidirect_navigation_menu_set_link';
    const DIGIDIRECT_NAVIGATION_MENU_SET = 'digidirect_navigation_menu_set';
    const DIGIDIRECT_NAVIGATION_MENU_ITEM_INFO_TABLE = 'digidirect_navigation_menu_item_info';
    const DIGIDIRECT_NAVIGATION_MENU_SET_STORE = 'digidirect_navigation_menu_set_store';

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /*
         * create item type table
         */
        $this->_createMenuItemTypeTable($installer);

        /**
         * Create entity table
         */
        $this->_createMainTable($installer);

        /**
         * create item info table
         */
        $this->_createMenuItemInfoTable($installer);

        /**
         * create navigation sets table
         */
        $this->_createMenuSetTable($installer);

        /**
         * create navigation set link table
         */
        $this->_createMenuSetMenuEntityLinkTable($installer);

        // create cms block type table
        $this->_createCmsBlockTypeTable($installer);

        // create category type table
        $this->_createCategoryTypeTable($installer);

        /**
         * Create custom link type menu item table
         */
        $this->_createCustomLinkTypeTable($installer);

        /**
         * Create link table - store/navigation set
         */
        $this->_createMenuSetStoreTable($installer);

        $this->_addCategoryDeleteTrigger($setup);
        $this->_addCMSBlockDeleteTrigger($setup);

        $installer->endSetup();
    }

    /**
     * Since version 1.3.0 this method does not terminate process if fails
     * Add mysql trigger on delete category
     *
     * @param $setup
     */
    protected function _addCategoryDeleteTrigger(SchemaSetupInterface $setup)
    {
        try {
            $connection = $setup->getConnection();

            /**
             * @var $menuItemInfoTable 'digidirect_navigation_menu_item_info' - table contains title and status etc
             */
            $menuItemInfoTable = $connection->getTableName('digidirect_navigation_menu_item_info');

            /**
             * @var $menuItemCategoryTypeTable - table contains category id
             */
            $menuItemCategoryTypeTable = $connection->getTableName('digidirect_navigation_menu_category_type');

            /**
             * Menu item information table columns
             */
            $menuItemInfoIdColumn = 'id';
            $menuItemInfoTriggeredColumn = 'status';

            /**
             * Category entity table column(PK)
             */
            $categoryEntityTableObservableColumn = 'entity_id';

            /**
             * Menu item category type columns
             */
            $menuItemCategoryTypeIdColumn = 'menu_item_id';
            $menuItemCategoryTypeCategoryIdColumn = 'category_id';

            /**
             * Generate trigger name
             */
            $event = 'DELETE';
            $triggerName = $connection->getTriggerName(
                $setup->getConnection()->getTableName('catalog_category_entity'),
                'BEFORE',
                'DELETE'
            );

            /**
             * Add unique postfix UMIS = update menu item status
             */
            $triggerName = $triggerName . '_umis';

            /**
             * Drop trigger if already exists
             */
            $connection->dropTrigger($triggerName);

            /** @var Trigger $trigger */
            $trigger = $this->triggerFactory->create()
                ->setName($triggerName)
                ->setTime(Trigger::TIME_BEFORE)
                ->setEvent($event)
                ->setTable($setup->getConnection()->getTableName('catalog_category_entity'));

            $statement = sprintf(
                "UPDATE %s as nit INNER JOIN %s as ncmi ON nit.%s = ncmi.%s AND ncmi.%s = OLD.%s SET nit.%s = 0;",
                $menuItemInfoTable,
                $menuItemCategoryTypeTable,
                $connection->quoteIdentifier($menuItemInfoIdColumn),
                $connection->quoteIdentifier($menuItemCategoryTypeIdColumn),
                $connection->quoteIdentifier($menuItemCategoryTypeCategoryIdColumn),
                $connection->quoteIdentifier($categoryEntityTableObservableColumn),
                $connection->quoteIdentifier($menuItemInfoTriggeredColumn)

            );
            $trigger->addStatement($statement);
            $connection->createTrigger($trigger);
        } catch(\Throwable $throwable) {
            return;
        }
    }

    /**
     * Since version 1.3.0 this method does not terminate process if fails
     * Add mysql trigger on delete category
     *
     * @param $setup
     */
    protected function _addCMSBlockDeleteTrigger(SchemaSetupInterface $setup)
    {
        try {
            $connection = $setup->getConnection();

            /**
             * @var $menuItemInfoTable 'digidirect_navigation_menu_item_info' - table contains title and status etc
             */
            $menuItemInfoTable = $connection->getTableName('digidirect_navigation_menu_item_info');

            /**
             * @var $menuItemCMSBlockTypeTable - table contains CMS block id
             */
            $menuItemCMSBlockTypeTable = $connection->getTableName('digidirect_navigation_menu_cms_block_type');

            /**
             * Menu item information table columns
             */
            $menuItemInfoIdColumn = 'id';
            $menuItemInfoTriggeredColumn = 'status';

            /**
             * CMS Block entity table column(PK)
             */
            $cmsBlockObservableColumn = 'block_id';

            /**
             * Menu item cms block type columns
             */
            $menuItemCMSBlockTypeIdColumn = 'menu_item_id';
            $menuItemCMSBlockTypeCMSBlockIdColumn = 'cms_block_id';

            $cmsBlockTable = $connection->getTableName('cms_block');

            /**
             * Generate trigger name
             */
            $event = 'DELETE';
            $triggerName = $connection->getTriggerName(
                $cmsBlockTable,
                'BEFORE',
                'DELETE'
            );

            /**
             * Add unique postfix UMIS = update menu item status
             */
            $triggerName = $triggerName . '_umis';

            /**
             * Drop trigger if already exists
             */
            $connection->dropTrigger($triggerName);

            /** @var Trigger $trigger */
            $trigger = $this->triggerFactory->create()
                ->setName($triggerName)
                ->setTime(Trigger::TIME_BEFORE)
                ->setEvent($event)
                ->setTable($cmsBlockTable);

            /**
             * nit = navigation info table
             * ncmi = navigation cms menu item
             */
            $statement = sprintf(
                "UPDATE %s as nit INNER JOIN %s as ncmi ON nit.%s = ncmi.%s AND ncmi.%s = OLD.%s SET nit.%s = 0;",
                $menuItemInfoTable,
                $menuItemCMSBlockTypeTable,
                $connection->quoteIdentifier($menuItemInfoIdColumn),
                $connection->quoteIdentifier($menuItemCMSBlockTypeIdColumn),
                $connection->quoteIdentifier($menuItemCMSBlockTypeCMSBlockIdColumn),
                $connection->quoteIdentifier($cmsBlockObservableColumn),
                $connection->quoteIdentifier($menuItemInfoTriggeredColumn)

            );
            $trigger->addStatement($statement);
            $connection->createTrigger($trigger);
        } catch (\Throwable $exception) {
            return;
        }
    }


    /**
     * Create Menu item type table
     *
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function _createMenuItemTypeTable(SchemaSetupInterface $installer)
    {
        $tableItemType = $installer->getConnection()
            ->newTable($installer->getTable(self::DIGIDIRECT_NAVIGATION_MENU_ITEM_TYPE_TABLE))
            ->addColumn(
                'type_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Navigation Item Type Id'
            )->addColumn(
                'menu_type_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Navigation Type Code'
            )->addColumn(
                'type_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Navigation Type Name'
            )->setComment(
                'Digidirect Navigation Item Types Table'
            );
        $installer->getConnection()->createTable($tableItemType);
    }

    /**
     * Create category type details table
     *
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function _createCategoryTypeTable(SchemaSetupInterface $installer)
    {
        $tableCategoryType = $installer->getConnection()
            ->newTable($installer->getTable(self::DIGIDIRECT_NAVIGATION_MENU_CATEGORY_TYPE_TABLE))
            ->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => true, // for "use_default" functionality
                ],
                'Category Id'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true, //use two fields combination as primary key
                ],
                'Store Id'
            )->addColumn(
                'use_category_hierarchy',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => true
                ],
                'Category hierarchy'
            )->addColumn(
                'nesting_level',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => 1
                ],
                'Nesting level'
            )
            ->addColumn(
                'menu_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Navigation Item Id'
            )->addForeignKey(
                $installer->getFkName(
                    self::DIGIDIRECT_NAVIGATION_MENU_CATEGORY_TYPE_TABLE,
                    'category_id',
                    'catalog_category_entity',
                    'entity_id'
                ),
                'category_id',
                $installer->getTable('catalog_category_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(self::DIGIDIRECT_NAVIGATION_MENU_CATEGORY_TYPE_TABLE, 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(self::DIGIDIRECT_NAVIGATION_MENU_CATEGORY_TYPE_TABLE, 'menu_item_id',
                    self::DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE, 'entity_id'),
                'menu_item_id',
                $installer->getTable(self::DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Digidirect Navigation Category Type Table'
            );
        $installer->getConnection()->createTable($tableCategoryType);
    }

    /**
     * Custom link details table
     *
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function _createCustomLinkTypeTable(SchemaSetupInterface $installer)
    {
        $tableCustomLinkType = $installer->getConnection()
            ->newTable($installer->getTable(self::DIGIDIRECT_NAVIGATION_MENU_CUSTOM_LINK_TYPE_TABLE))
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Store Id'
            )->addColumn(
                'menu_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Navigation Item Id'
            )->addColumn(
                'link',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => true
                ],
                'Navigation Item Link'
            )->addForeignKey(
                $installer->getFkName(
                    self::DIGIDIRECT_NAVIGATION_MENU_CUSTOM_LINK_TYPE_TABLE,
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    self::DIGIDIRECT_NAVIGATION_MENU_CUSTOM_LINK_TYPE_TABLE,
                    'menu_item_id',
                    self::DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE,
                    'entity_id'
                ),
                'menu_item_id',
                $installer->getTable(self::DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Digidirect Navigation Custom Link Type Table'
            );
        $installer->getConnection()->createTable($tableCustomLinkType);
    }

    /**
     * Cms Block Type details table
     *
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function _createCmsBlockTypeTable(SchemaSetupInterface $installer)
    {
        $tableCmsBlockType = $installer->getConnection()
            ->newTable($installer->getTable(self::DIGIDIRECT_NAVIGATION_MENU_CMS_BLOCK_TYPE_TABLE))
            ->addColumn(
                'cms_block_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => true,
                ],
                'Cms Block Id'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Store Id'
            )->addColumn(
                'menu_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Navigation Item Id'
            )->addColumn(
                'menu_item_is_link',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true],
                'Is Navigation Item a Link'
            )->addColumn(
                'link',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Navigation Item Link if Defined'
            )->addForeignKey(
                $installer->getFkName(self::DIGIDIRECT_NAVIGATION_MENU_CMS_BLOCK_TYPE_TABLE, 'cms_block_id', 'cms_block',
                    'block_id'),
                'cms_block_id',
                $installer->getTable('cms_block'),
                'block_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(self::DIGIDIRECT_NAVIGATION_MENU_CMS_BLOCK_TYPE_TABLE, 'store_id', 'store',
                    'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(self::DIGIDIRECT_NAVIGATION_MENU_CMS_BLOCK_TYPE_TABLE, 'menu_item_id',
                    self::DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE, 'entity_id'),
                'menu_item_id',
                $installer->getTable(self::DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Digidirect Navigation Cms Block Type Table'
            );
        $installer->getConnection()->createTable($tableCmsBlockType);
    }

    /**
     * Create entity table
     *
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function _createMainTable(SchemaSetupInterface $installer)
    {
        // create table entity
        $tableEntity = $installer->getConnection()
            ->newTable($installer->getTable(self::DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )->addColumn(
                'menu_item_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Navigation Item Code'
            )->addColumn(
                'type_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Navigation Item Type'
            )->addForeignKey(
                $installer->getFkName(self::DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE, 'type_id',
                    self::DIGIDIRECT_NAVIGATION_MENU_ITEM_TYPE_TABLE, 'type_id'),
                'type_id',
                $installer->getTable(self::DIGIDIRECT_NAVIGATION_MENU_ITEM_TYPE_TABLE),
                'type_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Digidirect Navigation Table'
            );
        $installer->getConnection()->createTable($tableEntity);
    }

    /**
     * Create item info table
     *
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function _createMenuItemInfoTable(SchemaSetupInterface $installer)
    {
        $tableAttributes = $installer->getConnection()
            ->newTable($installer->getTable(self::DIGIDIRECT_NAVIGATION_MENU_ITEM_INFO_TABLE))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Entity Id'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Store Id'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => true
                ],
                'Status'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => true
                ],
                'Navigation Item Title'
            )->addColumn(
                'path',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                [
                    'nullable' => true
                ],
                'Navigation Item Path'
            )->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => 0
                ],
                'Position'
            )->addColumn(
                'is_logged_in',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => true
                ],
                'Is For Logged In Users?'
            )->addColumn(
                'custom_options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [
                    'nullable' => true
                ],
                'Custom options (for developers)'
            )->addColumn(
                'level',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => true
                ],
                'Menu item level'
            )
            ->addColumn(
                'parent_menu_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => true,
                    'default' => 0
                ],
                'Parent Menu Item ID'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'digidirect_navigation_attributes',
                    'id',
                    self::DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE,
                    'entity_id'
                ),
                'id',
                $installer->getTable(self::DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('digidirect_navigation_attributes', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Digidirect Navigation Attributes'
            );
        $installer->getConnection()->createTable($tableAttributes);
    }

    /**
     * Create Menu set table
     *
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function _createMenuSetTable(SchemaSetupInterface $installer)
    {
        $tableSets = $installer->getConnection()
            ->newTable($installer->getTable(self::DIGIDIRECT_NAVIGATION_MENU_SET))
            ->addColumn(
                'set_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Navigation Set Id'
            )->addColumn(
                'set_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['nullable' => false],
                'Navigation Set Code'
            )->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Navigation Set Name'
            )->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Status'
            )->addIndex(
                $installer->getIdxName(
                    $installer->getTable(self::DIGIDIRECT_NAVIGATION_MENU_SET),
                    ['name', 'set_code'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['name', 'set_code'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )
            ->setComment(
                'Digidirect Navigation Sets Table'
            );
        $installer->getConnection()->createTable($tableSets);
    }

    /**
     * Menu - set Link table
     *
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function _createMenuSetMenuEntityLinkTable(SchemaSetupInterface $installer)
    {
        $tableNavigationSetLink = $installer->getConnection()
            ->newTable($installer->getTable(self::DIGIDIRECT_NAVIGATION_MENU_SET_LINK))
            ->addColumn(
                'menu_set_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Navigation Set Id'
            )->addColumn(
                'menu_entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Entity Id To Link'
            )->addForeignKey(
                $installer->getFkName(self::DIGIDIRECT_NAVIGATION_MENU_SET_LINK, 'menu_set_id',
                    self::DIGIDIRECT_NAVIGATION_MENU_SET, 'set_id'),
                'menu_set_id',
                $installer->getTable(self::DIGIDIRECT_NAVIGATION_MENU_SET),
                'set_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(self::DIGIDIRECT_NAVIGATION_MENU_SET_LINK, 'menu_entity_id',
                    self::DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE, 'entity_id'),
                'menu_entity_id',
                $installer->getTable(self::DIGIDIRECT_NAVIGATION_MAIN_ENTITY_TABLE),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Digidirect Navigation Set Links Table'
            );
        $installer->getConnection()->createTable($tableNavigationSetLink);
    }

    /**
     * Set Store link table
     *
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function _createMenuSetStoreTable(SchemaSetupInterface $installer)
    {
        $tableNavigationSetLink = $installer->getConnection()
            ->newTable($installer->getTable(self::DIGIDIRECT_NAVIGATION_MENU_SET_STORE))
            ->addColumn(
                'menu_set_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Navigation Set Id'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )
            ->addIndex(
                $installer->getIdxName(self::DIGIDIRECT_NAVIGATION_MENU_SET_STORE, ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName(self::DIGIDIRECT_NAVIGATION_MENU_SET_STORE, 'menu_set_id',
                    self::DIGIDIRECT_NAVIGATION_MENU_SET, 'set_id'),
                'menu_set_id',
                $installer->getTable(self::DIGIDIRECT_NAVIGATION_MENU_SET),
                'set_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(self::DIGIDIRECT_NAVIGATION_MENU_SET_STORE, 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Set store table'
            );
        $installer->getConnection()->createTable($tableNavigationSetLink);
    }
}
