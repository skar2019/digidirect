<?php

namespace Ewave\Blog\Sql;

use Ewave\Blog\Api\Data\CategoryContentInterface;
use Ewave\Blog\Model\ResourceModel\Category;
use Magento\Framework\DB\Select;
use Magento\Store\Model\Store;

class CategoryInformationJoin implements InformationJoinInterface, CurrentStoreContentCheckerInterface,
 IfNullProcessorInterface
{
    const DEFAULT_STORE_COLUMN_PREFIX = 'default_';

    /**
     * @var CategoryInformationSave
     */
    protected $categorySave;

    /**
     * CategoryInformationJoin constructor.
     *
     * @param CategoryInformationSave $categoryInformationSave
     */
    public function __construct(CategoryInformationSave $categoryInformationSave)
    {
        $this->categorySave = $categoryInformationSave;
    }

    /**
     * @param Select $select
     * @param int $storeId
     * @param null|string $mainTableAlias
     * @param null $id
     * @param array $fields
     * @return Select
     */
    public function join(
        Select $select,
        int $storeId,
        $mainTableAlias = Category::EWAVE_BLOG_CATEGORY_TABLE,
        $id = null,
        $fields = []
    ) {
        return $this->doJoin(
            $select,
            $storeId,
            $mainTableAlias,
            Category::EWAVE_BLOG_CATEGORY_INFORMATION_TABLE,
            '',
            $id,
            $fields
        );
    }

    /**
     * @param Select $select
     * @param int $storeId
     * @param string $mainTableAlias
     * @param string $joinTableAlias
     * @param string $columnPrefix
     * @param null $id
     * @param array $fields
     * @return Select
     */
    protected function doJoin(
        Select $select,
        int $storeId,
        $mainTableAlias = Category::EWAVE_BLOG_CATEGORY_TABLE,
        $joinTableAlias = Category::EWAVE_BLOG_CATEGORY_INFORMATION_TABLE,
        $columnPrefix = '',
        $id = null,
        $fields = []
    ) {
        if ($id && !$this->hasStoreViewContent($id, $storeId)) {
            $storeId = 0;
        }
        $selectFields = $this->getColumns();

        if (!empty($fields)) {
            $selectFields = $fields;
        }

        if ($columnPrefix) {
            foreach ($selectFields as $key => $field) {
                $selectFields[$columnPrefix . $field] = $field;
                unset($selectFields[$key]);
            }
        }

        $select->joinLeft(
            [$joinTableAlias => Category::EWAVE_BLOG_CATEGORY_INFORMATION_TABLE],
            $mainTableAlias . '.entity_id = ' . $joinTableAlias . '.' . CategoryContentInterface::CATEGORY_ID .
            ' AND ' . $joinTableAlias . '.' . CategoryContentInterface::STORE_ID . ' = ' . $storeId,
            $selectFields
        );
        return $select;
    }

    /**
     * @param Select $select
     * @param int $storeId
     * @param string $mainTableAlias
     * @param null $id
     * @param array $fields
     * @return null
     */
    public function joinDefault(
        Select $select,
        int $storeId = Store::DEFAULT_STORE_ID,
        $mainTableAlias = Category::EWAVE_BLOG_CATEGORY_TABLE,
        $id = null,
        $fields = []
    ) {
        $this->doJoin(
            $select,
            $storeId,
            $mainTableAlias,
            static::DEFAULT_STORE_COLUMN_PREFIX . Category::EWAVE_BLOG_CATEGORY_INFORMATION_TABLE,
            static::DEFAULT_STORE_COLUMN_PREFIX,
            $id,
            $fields
        );
    }

    /**
     * @param int $entityId
     * @param int $storeId
     * @return bool
     */
    public function hasStoreViewContent(int $entityId, int $storeId): bool
    {
        return $this->categorySave->hasStoreViewContent(...func_get_args());
    }

    /**
     * @return array
     */
    protected function getColumns()
    {
        return $this->categorySave->getMainTableColumns();
    }

    /**
     * @param mixed $column
     * @param mixed $value
     * @return \Zend_Db_Expr
     */
    public function processIfNull($column, $value)
    {
        return $this->categorySave->processIfNull($column, $value);
    }

    /**
     * @return string
     */
    public function getStoreViewSpecificTable(): string
    {
        return $this->categorySave->getStoreViewSpecificTable();
    }

    /**
     * @return string
     */
    public function getDefaultContentPrefix(): string
    {
        return $this->categorySave->getDefaultContentPrefix();
    }
}
