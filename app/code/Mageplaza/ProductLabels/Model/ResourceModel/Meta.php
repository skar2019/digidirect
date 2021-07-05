<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Meta
 * @package Mageplaza\ProductLabels\Model\ResourceModel
 */
class Meta extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_productlabels_rule_meta', 'meta_id');
    }

    /**
     * @param $date
     * @param int $storeId
     * @param int $customerGroupId
     * @param int $productId
     *
     * @return array
     */
    public function _getRulesFromProduct($date, $storeId, $customerGroupId, $productId)
    {
        $connection = $this->getConnection();

        if (is_string($date)) {
            $date = strtotime($date);
        }

        $select = $connection->select()
            ->from($this->getTable('mageplaza_productlabels_rule_meta'))
            ->where('store_id = 0 or store_id = ?', $storeId)
            ->where('customer_group_id = ?', $customerGroupId)
            ->where('product_id = ?', $productId)
            ->where('from_date = 0 or from_date <= ?', $date)
            ->where('to_date = 0 or to_date >= ?', $date)
            ->order('priority ' . Select::SQL_ASC)
            ->order('rule_id ' . Select::SQL_ASC);

        return $connection->fetchAll($select);
    }

    /**
     * @param $data
     *
     * @return $this
     */
    public function applyRule($data)
    {
        if (empty($data)) {
            return $this;
        }

        try {
            $this->getConnection()->insertMultiple($this->getMainTable(), $data);
        } catch (LocalizedException $e) {
            $this->_logger->critical($e->getMessage());
        }

        return $this;
    }

    /**
     * return @voild
     */
    public function truncateData()
    {
        try {
            $this->getConnection()->truncateTable($this->getMainTable());
        } catch (LocalizedException $e) {
            $this->_logger->critical($e->getMessage());
        }
    }
}
