<?php

namespace Ewave\Blog\Setup;

use Ewave\Blog\Api\Data\CategoryContentInterface;
use Ewave\Blog\Model\ResourceModel\Category;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Ewave\Blog\Api\Data\PostContentInterface;
use Ewave\Blog\Api\Data\PostInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var SchemaSetupInterface
     */
    protected $setup;

    /**
     * @var ModuleContextInterface
     */
    protected $context;

    /**
     * {@inheritdoc}
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->context = $context;
        $this->setup = $setup;

        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addTrendingImage($setup);
        }
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->changeCommentSize($setup);
        }
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->_createPostInformationTable($setup);
            $this->_upgradeBlogTableForStoreView($setup);
        }

        $this->process();
        $setup->endSetup();
    }

    /**
     * @param string $newVersion
     * @return mixed
     */
    protected function versionCompare($newVersion)
    {
        return version_compare($this->context->getVersion(), $newVersion, '<');
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function addTrendingImage(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable(PostInterface::EWAVE_BLOG_POST_TABLE),
            'image_trending',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'Trending Image',
            ]
        );
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return void
     */
    protected function changeCommentSize(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->modifyColumn(
            $setup->getTable('ewave_blog_comment'),
            'comment',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'Comment',
            ]
        );
    }

    /**
     * @return UpgradeSchema
     */
    protected function process(): self
    {
        foreach ($this->callbackByVersion() as $version => $functionName) {
            if ($this->versionCompare($version)) {
                $this->$functionName();
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function callbackByVersion()
    {
        return [
            '1.0.4' => 'createCategoryInformationTable',
            '1.0.5' => 'deleteColumns',
        ];
    }

    /**
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected function getConnection(): \Magento\Framework\DB\Adapter\AdapterInterface
    {
        return $this->setup->getConnection();
    }

    /**
     * @param string $tableName
     * @return string
     */
    protected function getTable(string $tableName): string
    {
        return $this->setup->getTable($tableName);
    }

    /**
     * @return UpgradeSchema
     * @throws \Zend_Db_Exception
     */
    protected function createCategoryInformationTable(): self
    {
        $tableCat = $this->getConnection()->newTable(
            $this->getTable(Category::EWAVE_BLOG_CATEGORY_INFORMATION_TABLE)
        )->addColumn(
            CategoryContentInterface::CATEGORY_ID,
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
            'Category ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Category Name'
        )->addColumn(
            'url_key',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Category URL Key'
        )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            [],
            'Category Status'
        )->addColumn(
            'meta_title',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Meta Keywords'
        )->addColumn(
            'meta_keywords',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Meta Keywords'
        )->addColumn(
            'meta_description',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Meta Description'
        )->addColumn(
            CategoryContentInterface::STORE_ID,
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['nullable' => false, 'unsigned' => true],
            'Store Id'
        )->addIndex(
            $this->setup->getIdxName(
                Category::EWAVE_BLOG_CATEGORY_INFORMATION_TABLE,
                [CategoryContentInterface::STORE_ID, CategoryContentInterface::CATEGORY_ID],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [CategoryContentInterface::STORE_ID, CategoryContentInterface::CATEGORY_ID],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $this->getFkName(
                Category::EWAVE_BLOG_CATEGORY_INFORMATION_TABLE,
                CategoryContentInterface::CATEGORY_ID,
                Category::EWAVE_BLOG_CATEGORY_TABLE,
                'entity_id'
            ),
            CategoryContentInterface::CATEGORY_ID,
            Category::EWAVE_BLOG_CATEGORY_TABLE,
            'entity_id',
            AdapterInterface::FK_ACTION_CASCADE
        )->addForeignKey(
            $this->getFkName(
                Category::EWAVE_BLOG_CATEGORY_INFORMATION_TABLE,
                CategoryContentInterface::STORE_ID,
                'store',
                'store_id'
            ),
            CategoryContentInterface::STORE_ID,
            'store',
            'store_id',
            AdapterInterface::FK_ACTION_CASCADE
        )->setComment(
            'Category Information Table'
        );

        $this->getConnection()->createTable($tableCat);
        return $this;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    protected function _createPostInformationTable(SchemaSetupInterface $setup)
    {
        $newTable = $setup->getConnection()->newTable(
            $setup->getTable(PostContentInterface::EWAVE_BLOG_POST_INFORMATION_TABLE)
        )->addColumn(
            PostContentInterface::EWAVE_BLOG_POST_INFORMATION_TABLE_ID,
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false],
            'Post ID'
        )->addColumn(
            PostContentInterface::EWAVE_BLOG_POST_INFORMATION_TABLE_TITLE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Title'
        )->addColumn(
            PostContentInterface::EWAVE_BLOG_POST_INFORMATION_TABLE_CONTENT,
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Content'
        )->addColumn(
            PostContentInterface::EWAVE_BLOG_POST_INFORMATION_TABLE_SHORT_CONTENT,
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Short Content'
        )->addColumn(
            'meta_title',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Meta Keywords'
        )->addColumn(
            'meta_keywords',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Meta Keywords'
        )->addColumn(
            'meta_description',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Meta Description'
        )->addColumn(
            PostContentInterface::STORE_ID,
            Table::TYPE_SMALLINT,
            5,
            ['nullable' => false, 'unsigned' => true],
            'Store Id'
        )->addIndex(
            $setup->getIdxName(
                PostContentInterface::EWAVE_BLOG_POST_INFORMATION_TABLE,
                [PostContentInterface::STORE_ID, PostContentInterface::EWAVE_BLOG_POST_INFORMATION_TABLE_ID],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [PostContentInterface::STORE_ID, PostContentInterface::EWAVE_BLOG_POST_INFORMATION_TABLE_ID],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $setup->getFkName(
                PostContentInterface::EWAVE_BLOG_POST_INFORMATION_TABLE,
                PostContentInterface::EWAVE_BLOG_POST_INFORMATION_TABLE_ID,
                PostInterface::EWAVE_BLOG_POST_TABLE,
                'entity_id'
            ),
            PostContentInterface::EWAVE_BLOG_POST_INFORMATION_TABLE_ID,
            PostInterface::EWAVE_BLOG_POST_TABLE,
            'entity_id',
            AdapterInterface::FK_ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName(
                PostContentInterface::EWAVE_BLOG_POST_INFORMATION_TABLE,
                PostContentInterface::STORE_ID,
                'store',
                'store_id'
            ),
            PostContentInterface::STORE_ID,
            'store',
            'store_id',
            AdapterInterface::FK_ACTION_CASCADE
        )->setComment(
            'Post Information Table'
        );

        $setup->getConnection()->createTable($newTable);

        return $this;
    }
       
    /**
     * @param string $priTableName
     * @param string $priColumnName
     * @param string $refTableName
     * @param string $refColumnName
     * @return string
     */
    protected function getFkName($priTableName, $priColumnName, $refTableName, $refColumnName)
    {
        return $this->setup->getFkName($priTableName, $priColumnName, $refTableName, $refColumnName);
    }

    /**
     * @return UpgradeSchema
     */
    protected function deleteColumns(): self
    {
        $columns = [
            'name',
            'status',
            'order',
            'meta_title',
            'meta_keywords',
            'meta_description',
            'url_key',
        ];
        foreach ($columns as $column) {
            $this->getConnection()->dropColumn($this->getTable(Category::EWAVE_BLOG_CATEGORY_TABLE), $column);
        }

        return $this;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @return $this
     */
    protected function _upgradeBlogTableForStoreView($setup)
    {
        $columns = [
            'title',
            'content',
            'short_content',
            'meta_title',
            'meta_keywords',
            'meta_description',
        ];
        foreach ($columns as $column) {
            $setup->getConnection()->dropColumn($setup->getTable(PostInterface::EWAVE_BLOG_POST_TABLE), $column);
        }

        return $this;
    }
}
