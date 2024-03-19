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
 */

namespace Mageplaza\ProductLabels\Model\ResourceModel\Meta;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mageplaza\ProductLabels\Model\ResourceModel\Meta;
use Zend_Db_Select;

/**
 * Class Collection
 * @package Mageplaza\ProductLabels\Model\ResourceModel\Meta
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'meta_id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_productlabels_rule_meta_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'meta_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Mageplaza\ProductLabels\Model\Meta::class,
            Meta::class
        );
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);

        return $countSelect;
    }
}
