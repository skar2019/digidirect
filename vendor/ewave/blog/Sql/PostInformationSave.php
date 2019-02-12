<?php

namespace Ewave\Blog\Sql;

use Ewave\Blog\Api\Data\PostContentInterface;
use Ewave\Blog\Model\CurrentStoreFetcher;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class PostInformationSave extends AbstractDb implements InformationSaveInterface, CurrentStoreContentCheckerInterface
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
     * @return void
     */
    protected function _construct()
    {
        $this->_setMainTable(PostContentInterface::EWAVE_BLOG_POST_INFORMATION_TABLE);
    }

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
     * @param \Ewave\Blog\Model\Post $entity
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return null
     */
    public function saveInformation($entity)
    {
        $data = [
            PostContentInterface::STORE_ID => $this->currentStore->getCurrentStoreId(),
            PostContentInterface::EWAVE_BLOG_POST_INFORMATION_TABLE_ID => $entity->getId(),
        ];

        foreach ($entity->getData() as $key => $value) {
            if (!is_scalar($value) || isset($data[$key])) {
                continue;
            }
            $data[$key] = $value;
        }

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
     * @param int $entityId
     * @param int $storeId
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function hasStoreViewContent(int $entityId, int $storeId): bool
    {
        $cacheKey = $entityId . $storeId . $this->getMainTable();
        if (!isset($this->cacheByEntity[$cacheKey])) {

            $select = $this->getConnection()->select()
                ->from($this->getMainTable())
                ->where(PostContentInterface::STORE_ID . '= ?', $storeId)
                ->where(PostContentInterface::EWAVE_BLOG_POST_INFORMATION_TABLE_ID . '= ?', $entityId);
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
     * @return string
     */
    public function getDefaultContentPrefix()
    {
        return PostInformationJoin::DEFAULT_STORE_COLUMN_PREFIX;
    }
}
