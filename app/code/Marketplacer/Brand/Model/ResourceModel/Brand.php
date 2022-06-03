<?php

namespace Marketplacer\Brand\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Marketplacer\Brand\Api\Data\BrandInterface;

/**
 * Class Brand
 * @package Marketplacer\Brand\Model\ResourceModel
 */
class Brand extends AbstractDb
{
    public const BRAND_TABLE_NAME = 'marketplacer_brand';

    /**
     * Initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init(static::BRAND_TABLE_NAME, BrandInterface::ROW_ID);
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getAllBrandIds()
    {
        $connection = $this->getConnection();

        $idsSelect = $connection
            ->select()
            ->from($this->getMainTable())
            ->reset(Select::COLUMNS)
            ->columns(BrandInterface::BRAND_ID)
            ->distinct(true);

        return $connection->fetchCol($idsSelect);
    }

    /**
     * @param BrandInterface | AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (null === $object->getStatus()) {
            $object->setStatus(BrandInterface::STATUS_ENABLED);
        }

        return parent::_beforeSave($object);
    }

    /**
     * @param BrandInterface | AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        if ($object->getData('_regenerate_url')
            || ($object->getOrigData() && ($object->dataHasChangedFor('name') || $object->dataHasChangedFor('url_key')))
        ) {
            $object->processUrlRewrites();
        }

        return parent::_afterSave($object);
    }

    /**
     * @param BrandInterface | AbstractModel $object
     * @return Brand
     * @throws LocalizedException
     */
    protected function _afterDelete(AbstractModel $object)
    {
        $connection = $this->getConnection();

        //remove records related to other stores if exist
        $connection->delete(
            $this->getMainTable(),
            $connection->quoteInto('brand_id = ?', $object->getBrandId())
            . ' AND '
            . $connection->quoteInto('store_id != ?', $object->getStoreId())
        );

        $object->deleteUrlRewrites();

        return parent::_afterDelete($object);
    }
}
