<?php

declare(strict_types=1);

namespace Amasty\BannerSlider\Model\ResourceModel;

use Amasty\BannerSlider\Api\Data\BannerInterface;
use Amasty\BannerSlider\Model\ImageProcessor;
use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\Store;

class Banner extends AbstractDb
{
    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        ImageProcessor $imageProcessor,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->imageProcessor = $imageProcessor;
    }

    protected function _construct()
    {
        $this->_init(BannerInterface::STATIC_TABLE_NAME, BannerInterface::ID);
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object): AbstractDb
    {
        $this->saveImage($object);

        return parent::_beforeSave($object);
    }

    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object): AbstractDb
    {
        $connection = $this->getConnection();
        $dynamicTable = $this->getTable(BannerInterface::DYNAMIC_TABLE_NAME);

        $connection->insertOnDuplicate(
            $dynamicTable,
            $object->getDynamicData()
        );

        return parent::_afterSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function saveImage(\Magento\Framework\Model\AbstractModel $object)
    {
        $image = $object->getData(BannerInterface::IMAGE);
        if ($object->dataHasChangedFor(BannerInterface::IMAGE) && $image) {
            $savedImage = $this->imageProcessor->saveImage($image);
            if ($image != $savedImage) {
                $object->setData(BannerInterface::IMAGE, $savedImage);
            }
        }
    }

    public function getAllImages(): array
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable(BannerInterface::DYNAMIC_TABLE_NAME), BannerInterface::IMAGE);

        return $this->getConnection()->fetchCol($select) ?? [];
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $dynamicContentTable = $this->getTable(BannerInterface::DYNAMIC_TABLE_NAME);
        $storeId = (int) $object->getData(BannerInterface::STORE_ID);

        $select = $this->joinDynamicTable($select, $dynamicContentTable, Store::DEFAULT_STORE_ID);
        if ($storeId !== Store::DEFAULT_STORE_ID) {
            $select = $this->joinDynamicTable($select, $dynamicContentTable, $storeId);
            $select = $this->selectColumns($select, BannerInterface::DYNAMIC_FIELDS, $storeId);
        }

        $select->columns(sprintf(
            '%s AS %s',
            $this->getTable(BannerInterface::STATIC_TABLE_NAME) . '.' . BannerInterface::ID,
            BannerInterface::ID
        ));

        return $select;
    }

    private function selectColumns(Select $select, array $columns, int $storeId = 0): Select
    {
        $currentColumns = [];
        foreach ($columns as $column) {
            $currentColumns[$column] = $this->getConnection()->getIfNullSql(
                sprintf('%s_%s.%s', BannerInterface::DYNAMIC_TABLE_NAME, $storeId, $column),
                sprintf('%s_%s.%s', BannerInterface::DYNAMIC_TABLE_NAME, Store::DEFAULT_STORE_ID, $column)
            );
        }

        return $select->columns($currentColumns);
    }

    private function joinDynamicTable(Select $select, string $dynamicContentTable, int $storeId = 0): Select
    {
        $alias = sprintf('%s_%s', BannerInterface::DYNAMIC_TABLE_NAME, $storeId);
        $select->joinLeft(
            [$alias => $dynamicContentTable],
            sprintf('%s.id = %s.id AND %s.store_id = %s', $alias, $this->getMainTable(), $alias, $storeId)
        );

        return $select;
    }
}
