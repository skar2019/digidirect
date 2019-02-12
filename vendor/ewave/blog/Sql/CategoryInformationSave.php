<?php

namespace Ewave\Blog\Sql;

use Ewave\Blog\Api\Data\CategoryContentInterface;
use Ewave\Blog\Model\CurrentStoreFetcher;
use Ewave\Blog\Model\ResourceModel\Category;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class CategoryInformationSave extends AbstractDb implements InformationSaveInterface,
 CurrentStoreContentCheckerInterface, IfNullProcessorInterface
{
    /**
     * @var CurrentStoreFetcher
     */
    protected $currentStore;

    /**
     * @var array
     */
    protected $cacheByEntity = [];

    /**
     * @var null|array
     */
    protected $tableDescription = null;

    /**
     * CategoryInformationSave constructor.
     *
     * @param Context $context
     * @param CurrentStoreFetcher $currentStoreFetcher
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        CurrentStoreFetcher $currentStoreFetcher,
        $connectionName = null
    ) {
        $this->currentStore = $currentStoreFetcher;
        parent::__construct($context, $connectionName);
    }

    /**
     * @param \Ewave\Blog\Model\Category $entity
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveInformation($entity)
    {
        $data = [
            CategoryContentInterface::STORE_ID => $this->currentStore->getCurrentStoreId(),
            CategoryContentInterface::CATEGORY_ID => $entity->getId(),
        ];

        foreach ($entity->getData() as $key => $value) {
            if (!is_scalar($value) || isset($data[$key])) {
                continue;
            }
            $data[$key] = $value;
        }

        $table = $this->getConnection()->describeTable($this->getMainTable());
        $columns = $this->getConnection()->describeTable($this->getMainTable());

        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $columns)) {
                unset($data[$key]);
            }
        }

        $this->getConnection()->insertOnDuplicate(
            $this->getMainTable(),
            [
                $data,
            ]
        );
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_setMainTable(Category::EWAVE_BLOG_CATEGORY_INFORMATION_TABLE);
    }

    /**
     * @param int $entityId
     * @param int $storeId
     * @return bool
     */
    public function hasStoreViewContent(int $entityId, int $storeId): bool
    {
        $cacheKey = $entityId . $storeId . $this->getMainTable();
        if (!isset($this->cacheByEntity[$cacheKey])) {

            $select = $this->getConnection()->select()
                ->from($this->getMainTable())
                ->where(CategoryContentInterface::STORE_ID . '= ?', $storeId)
                ->where(CategoryContentInterface::CATEGORY_ID . '= ?', $entityId);
            $has = $this->getConnection()->fetchRow($select);

            $this->cacheByEntity[$cacheKey] = !empty($has);
        }

        return $this->cacheByEntity[$cacheKey] ?? false;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMainTableColumns()
    {
        if (null === $this->tableDescription) {
            $this->tableDescription = $this->getConnection()->describeTable($this->getMainTable());
        }
        return array_keys($this->tableDescription);
    }

    /**
     * @param mixed $column
     * @param mixed $value
     * @return \Zend_Db_Expr
     */
    public function processIfNull($column, $value)
    {
        $generalExpr = $this->getStoreViewSpecificTable() . '.' . $column . ' =' . $value;
        return $this->getConnection()->getIfNullSql(
            $generalExpr,
            CategoryInformationJoin::DEFAULT_STORE_COLUMN_PREFIX . $generalExpr
        );
    }

    /**
     * @return string
     */
    public function getStoreViewSpecificTable(): string
    {
        return Category::EWAVE_BLOG_CATEGORY_INFORMATION_TABLE;
    }

    /**
     * @return string
     */
    public function getDefaultContentPrefix(): string
    {
        return CategoryInformationJoin::DEFAULT_STORE_COLUMN_PREFIX;
    }
}
