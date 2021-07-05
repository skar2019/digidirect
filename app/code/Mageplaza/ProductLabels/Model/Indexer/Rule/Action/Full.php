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

namespace Mageplaza\ProductLabels\Model\Indexer\Rule\Action;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Profiler;
use Mageplaza\ProductLabels\Helper\Data;
use Mageplaza\ProductLabels\Model\Rule;

/**
 * Class Full
 * @package Mageplaza\ProductLabels\Model\Indexer\Rule\Action
 */
class Full
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Full constructor.
     *
     * @param ResourceConnection $resource
     * @param Data $helperData
     */
    public function __construct(
        ResourceConnection $resource,
        Data $helperData
    ) {
        $this->resource   = $resource;
        $this->helperData = $helperData;
    }

    /**
     * Reindex information about rule relations with products.
     *
     * @param Rule $rule
     * @param $batchCount
     *
     * @return bool
     */
    public function execute(Rule $rule, $batchCount)
    {
        if (!$rule->getEnabled()) {
            return false;
        }

        $connection = $this->resource->getConnection();
        $indexTable = $this->resource->getTableName('mageplaza_productlabels_rule_meta');
        $storeIds   = $rule->getStoreIds();
        if (!is_array($storeIds)) {
            $storeIds = explode(',', $storeIds);
        }

        $customerGroupIds = $rule->getCustomerGroupIds();
        if (!is_array($customerGroupIds)) {
            $customerGroupIds = explode(',', $customerGroupIds);
        }
        $ruleId = $rule->getId();
        $rows   = [];

        foreach ($storeIds as $storeId) {
            Profiler::start('__MATCH_PRODUCTS__');
            $productIds = $this->helperData->getProductIds($rule, $storeId);
            Profiler::stop('__MATCH_PRODUCTS__');

            foreach ($productIds as $productId) {
                foreach ($customerGroupIds as $customerGroupId) {
                    $rows[] = [
                        'rule_id'           => $ruleId,
                        'product_id'        => $productId,
                        'template_url'      => $rule->getListTemplate(),
                        'img_url'           => $this->helperData->getSrcImg($rule, $storeId),
                        'label'             => $rule->getListLabel(),
                        'label_style'       => $this->helperData->getLabelStyle($rule),
                        'label_fontsize'    => $rule->getListFontSize(),
                        'label_position'    => $rule->getListPosition(),
                        'custom_css'        => $this->helperData->getCategoryCustomCss($rule, $productId),
                        'from_date'         => strtotime($rule->getFromDate()),
                        'to_date'           => strtotime($rule->getToDate()),
                        'customer_group_id' => $customerGroupId,
                        'store_id'          => $storeId,
                        'stop_process'      => $rule->getStopProcess(),
                        'priority'          => $rule->getPriority()
                    ];

                    if (count($rows) === $batchCount) {
                        $connection->insertMultiple($indexTable, $rows);
                        $rows = [];
                    }
                }
            }
        }
        if (!empty($rows)) {
            $connection->insertMultiple($indexTable, $rows);
        }

        return true;
    }
}
