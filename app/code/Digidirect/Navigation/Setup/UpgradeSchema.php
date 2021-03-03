<?php
namespace Digidirect\Navigation\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade the module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var ModuleContextInterface
     */
    protected $context;

    /**
     * @var SchemaSetupInterface
     */
    protected $setup;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->context = $context;
        $this->setup = $setup;
        $this->connection = $setup->getConnection();

        $this->setup->startSetup();

        if ($this->compareVersions('1.0.1')) {
            $this->modifyIsLoggedInColumn();
        }

        if ($this->compareVersions('1.0.2')) {
            $this->modifyIsLoggedInColumnSetNullable();
        }

        if ($this->compareVersions('1.0.3')) {
            $this->addContentColumn();
        }

        if ($this->compareVersions('1.0.4')) {
            $this->createMenuItemStoreRelationTable();
        }

        $this->setup->endSetup();
    }

    /**
     * Create relation table menu_item_id - store_id
     *
     * @return void
     */
    protected function createMenuItemStoreRelationTable()
    {
        $tableName = 'digidirect_navigation_menu_item_store_relation';
        if (!$this->isTableExists($tableName)) {

            $table = $this->connection
                ->newTable($this->setup->getTable($tableName))
                ->addColumn(
                    'menu_store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    'Store Id'
                )->addColumn(
                    'menu_entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                    ],
                    'Navigation Item Id'
                )->addForeignKey(
                    $this->setup->getFkName(
                        $tableName,
                        'menu_store_id',
                        'store',
                        'store_id'
                    ),
                    'menu_store_id',
                    $this->setup->getTable('store'),
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->addForeignKey(
                    $this->setup->getFkName(
                        $tableName,
                        'menu_entity_id',
                        'digidirect_navigation_menu_entity',
                        'entity_id'
                    ),
                    'menu_entity_id',
                    $this->setup->getTable('digidirect_navigation_menu_entity'),
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->setComment(
                    'Digidirect Menu Item Store Relation'
                );

            $this->connection->createTable($table);
        }
    }

    /**
     * Add new fields to digidirect_aa table: aa_listing_enabled, aa_url_key, aa_meta_title, aa_meta_desc
     *
     * @return void
     */
    protected function modifyIsLoggedInColumn()
    {
        $tableName = 'digidirect_navigation_menu_item_info';
        $columnName = 'is_logged_in';
        if ($this->isTableExists($tableName) && $this->isColumnExists($tableName, 'is_logged_in')) {
            $this->connection->modifyColumn(
                $tableName,
                $columnName,
                [
                    'default' => \Digidirect\Navigation\Model\Menu::MENU_ITEM_FOR_ALL_USERS,
                    'type' => Table::TYPE_SMALLINT,
                    'unsigned' => true,
                    'nullable' => false,
                    'comment' => 'Is for logged in users?',
                ]
            );
        }

        $tableName = 'digidirect_navigation_menu_entity';
        $columnName = 'menu_item_code';
        if ($this->isTableExists($tableName) && $this->isColumnExists($tableName, $columnName)) {

            $select = $this->connection->select()
                ->from($tableName, ['entity_id']);
            $result = $this->connection->fetchCol($select);

            if (!empty($result)) {
                foreach ($result as $record) {
                    $this->connection->update(
                        $tableName,
                        ['menu_item_code' => $record],
                        $this->connection->quoteInto('entity_id = ?', $record)
                    );
                }
            }

            $this->connection->addIndex(
                $tableName,
                'menu_type_code_unique_index',
                [$columnName],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            );
        }
    }

    /**
     * Set nullable is_logged_in column
     *
     * @return void
     */
    protected function modifyIsLoggedInColumnSetNullable()
    {
        $tableName = 'digidirect_navigation_menu_item_info';
        $columnName = 'is_logged_in';
        if ($this->isTableExists($tableName) && $this->isColumnExists($tableName, 'is_logged_in')) {
            $this->connection->modifyColumn(
                $tableName,
                $columnName,
                [
                    'default' => \Digidirect\Navigation\Model\Menu::MENU_ITEM_FOR_ALL_USERS,
                    'type' => Table::TYPE_SMALLINT,
                    'unsigned' => true,
                    'nullable' => true,
                    'comment' => 'Is for logged in users?',
                ]
            );
        }
    }

    /**
     * Add content column
     * @return void
     */
    protected function addContentColumn()
    {
        $tableName = 'digidirect_navigation_menu_item_info';
        $columnName = 'content';
        if ($this->isTableExists($tableName) && !$this->isColumnExists($tableName, $columnName)) {
            $this->connection->addColumn(
                $tableName,
                $columnName,
                [
                    'default' => null,
                    'type' => Table::TYPE_TEXT,
                    'unsigned' => true,
                    'nullable' => true,
                    'comment' => 'Wysiwyg Content For menu item',
                ]
            );
        }
    }

    /**
     * Compare versions
     *
     * @param string $new
     * @return bool
     */
    protected function compareVersions($new)
    {
        return version_compare($this->context->getVersion(), $new, '<');
    }

    /**
     * Check if column is exists
     *
     * @param string $tableName
     * @param string $columnName
     * @return bool
     */
    protected function isColumnExists($tableName, $columnName)
    {
        return $this->connection->tableColumnExists($tableName, $columnName);
    }

    /**
     * Check if column is exists
     *
     * @param string $tableName
     * @param string $columnName
     * @return bool
     */
    protected function deleteColumn($tableName, $columnName)
    {
        return $this->connection->dropColumn($tableName, $columnName);
    }

    /**
     * Check if table is exists
     *
     * @param string $tableName
     * @return bool
     */
    protected function isTableExists($tableName)
    {
        return $this->connection->isTableExists($tableName);
    }
}
