<?php

namespace Ewave\Banner\Setup;

use Ewave\Banner\Model\ResourceModel\Video;
use Magento\Framework\DB\Adapter\AdapterInterface;
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
    protected $_context;

    /**
     * @var SchemaSetupInterface
     */
    protected $_setup;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->_context = $context;
        $this->_setup = $setup;
        $this->_connection = $setup->getConnection();

        $this->_setup->startSetup();

        if ($this->_compareVersions('1.0.2')) {
            $this->addAltAttribute();
        }

        if ($this->_compareVersions('1.0.3')) {
            $this->createBannerVideoTable();
        }

        if ($this->_compareVersions('1.0.4')) {
            $this->addNavigationTypeFields();
        }

        if ($this->_compareVersions('1.0.5')) {
            $this->setVideoTitleColumnNullable();
        }

        if ($this->_compareVersions('1.0.6')) {
            $this->addSizeVideoFields();
        }
        $this->_setup->endSetup();
    }

    /**
     * @return $this
     */
    protected function addSizeVideoFields()
    {
        $tableName = Video::getEntityTableName();
        if (!$this->_isTableExists($tableName)) {
            return $this;
        }

        $columnName = Video::SIZE_COLUMN;
        if (!$this->_isColumnExists($tableName, $columnName)) {
            $this->_connection->addColumn(
                $this->_setup->getTable($tableName),
                $columnName,
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Video Size',
                    'default' => null,
                ]
            );
        }
        return $this;
    }

    /**
     * Add columns
     *
     * @return void
     */
    protected function addNavigationTypeFields()
    {
        $tableName = 'ewave_banner_attributes';
        if (!$this->_isTableExists($tableName)) {
            return;
        }

        $columnName = 'navigation_title_type';
        if (!$this->_isColumnExists($tableName, $columnName)) {
            $this->_connection->addColumn(
                $this->_setup->getTable($tableName),
                $columnName,
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment' => 'Banner Navigation Type',
                    'default' => \Ewave\Banner\Model\Attributes\NavigationTitle::NAVIGATION_TYPE_TITLE_WYSIWYG,
                ]
            );
        }

        $columnName = 'navigation_image';
        if (!$this->_isColumnExists($tableName, $columnName)) {
            $this->_connection->addColumn(
                $this->_setup->getTable($tableName),
                $columnName,
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Banner Navigation Image',
                ]
            );
        }
    }

    /**
     * Create banner video table
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     *
     * @return void
     */
    protected function createBannerVideoTable()
    {
        $bannerVideoTable = 'ewave_banner_video';

        if (!$this->_isTableExists($bannerVideoTable)) {
            $table = $this->_connection->newTable(
                $this->_setup->getTable($bannerVideoTable)
            )->addColumn(
                'video_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'identity' => true],
                'Entity ID'
            )->addColumn(
                'banner_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                ],
                'Banner ID'
            )->addColumn(
                'video_title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false,
                ],
                'Title'
            )->addColumn(
                'video_folder',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false,
                ],
                'Video Folder'
            )->addColumn(
                'use_video_as_a_preview',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                [
                    'nullable' => true,
                ],
                'Use video as a preview'
            )->addColumn(
                'preview_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false,
                ],
                'Preview image path'
            )->addColumn(
                'video',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Video file path'
            )->addColumn(
                'video_role_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [
                    'nullable' => true,
                ],
                'Role IDs'
            )->addColumn(
                'play_video_automatically_for_desktop',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                [
                    'nullable' => true,
                ],
                'Play video automatically for desktop'
            )->addColumn(
                'play_video_automatically_for_mobile',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                [
                    'nullable' => true,
                ],
                'Play video automatically for Mobile'
            )->addColumn(
                'play_video_after',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                20,
                [
                    'nullable' => true,
                ],
                'Play video after x seconds'
            )->addColumn(
                'play_video_in_a_loop',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                20,
                [
                    'nullable' => true,
                ],
                'Play video in a loop'
            )->addColumn(
                'allow_video_popup',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                20,
                [
                    'nullable' => true,
                ],
                'Allow video popup'
            )->addIndex(
                $this->_setup->getIdxName(
                    $bannerVideoTable,
                    ['banner_id', 'video'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['banner_id', 'video'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addForeignKey(
                $this->_setup->getFkName(
                    $bannerVideoTable,
                    'banner_id',
                    TableConstants::TABLE,
                    'banner_id'
                ),
                'banner_id',
                $this->_setup->getTable(TableConstants::TABLE),
                'banner_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment('Banner video table');
            $this->_connection->createTable($table);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function addAltAttribute()
    {
        $tableName = 'ewave_banner_attributes';
        if (!$this->_isTableExists($tableName)) {
            return;
        }

        $columnName = 'alt';
        if (!$this->_isColumnExists($tableName, $columnName)) {
            $this->_connection->addColumn(
                $this->_setup->getTable($tableName),
                $columnName,
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Banner Alt text',
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
    protected function _compareVersions($new)
    {
        return version_compare($this->_context->getVersion(), $new, '<');
    }

    /**
     * Check if column is exists
     *
     * @param string $tableName
     * @param string $columnName
     * @return bool
     */
    protected function _isColumnExists($tableName, $columnName)
    {
        return $this->_connection->tableColumnExists($tableName, $columnName);
    }

    /**
     * Check if column is exists
     *
     * @param string $tableName
     * @param string $columnName
     * @return bool
     */
    protected function _deleteColumn($tableName, $columnName)
    {
        return $this->_connection->dropColumn($tableName, $columnName);
    }

    /**
     * Check if table is exists
     *
     * @param string $tableName
     * @return bool
     */
    protected function _isTableExists($tableName)
    {
        return $this->_connection->isTableExists($tableName);
    }

    /**
     * @return void
     */
    protected function setVideoTitleColumnNullable()
    {
        $bannerVideoTable = 'ewave_banner_video';
        $this->_connection->modifyColumn(
            $bannerVideoTable,
            'video_title',
            [
                'type' => 'text',
                'default' => '',
                'nullable' => true,
            ]
        );
    }
}
