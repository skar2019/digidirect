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
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductLabels\Model\ResourceModel\Rule\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * Class Collection
 * @package Mageplaza\ProductLabels\Model\ResourceModel\Rule\Grid
 */
class Collection extends SearchResult
{
    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     *
     * @return $this|SearchResult
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_filter') {
            $this->getSelect()->where("store_ids LIKE '%{$condition['eq']}%'");

            return $this;
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
