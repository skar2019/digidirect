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

namespace Mageplaza\ProductLabels\Model\Indexer\Rule;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Mageplaza\ProductLabels\Helper\Data;
use Mageplaza\ProductLabels\Model\Indexer\ProductLoader;
use Mageplaza\ProductLabels\Model\ResourceModel\Rule as ResourceRule;
use Mageplaza\ProductLabels\Model\ResourceModel\Rule\CollectionFactory;
use Mageplaza\ProductLabels\Model\Rule;

/**
 * Class AbstractAction
 * @package Mageplaza\ProductLabels\Model\Indexer\Rule
 */
class AbstractAction
{
    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var mixed
     */
    protected $productLoader;

    /**
     * AbstractAction constructor.
     *
     * @param ResourceConnection $resource
     * @param CollectionFactory $ruleCollectionFactory
     * @param Data $helperData
     * @param ProductLoader|null $productLoader
     */
    public function __construct(
        ResourceConnection $resource,
        CollectionFactory $ruleCollectionFactory,
        Data $helperData,
        ProductLoader $productLoader = null
    ) {
        $this->resource              = $resource;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->helperData            = $helperData;
        $this->connection            = $resource->getConnection();
        $this->productLoader         = $productLoader ?: ObjectManager::getInstance()->get(ProductLoader::class);
    }

    /**
     * Reindex by ids
     *
     * @param array $ids
     *
     * @throws Exception
     */
    protected function doReindexByIds($ids)
    {
        $this->cleanByIds($ids);

        $products = $this->productLoader->getProducts($ids);
        foreach ($this->getActiveRules() as $rule) {
            $productIds = $this->helperData->getProductIds($rule);
            foreach ($products as $product) {
                if (!in_array($product->getId(), $productIds)) {
                    continue;
                }
                $this->applyRule($rule, $product);
            }
        }
    }

    /**
     * Clean by product ids
     *
     * @param array $productIds
     */
    protected function cleanByIds($productIds)
    {
        $query = $this->connection->deleteFromSelect(
            $this->connection
                ->select()
                ->from($this->resource->getTableName('mageplaza_productlabels_rule_meta'), 'product_id')
                ->distinct()
                ->where('product_id IN (?)', $productIds),
            $this->resource->getTableName('mageplaza_productlabels_rule_meta')
        );
        $this->connection->query($query);
    }

    /**
     * Get active rules
     *
     * @return ResourceRule\Collection
     */
    protected function getActiveRules()
    {
        return $this->ruleCollectionFactory->create()->addFieldToFilter('enabled', 1);
    }

    /**
     * @param Rule $rule
     * @param Product $product
     *
     * @return $this
     * @throws Exception
     */
    protected function applyRule(Rule $rule, $product)
    {
        $ruleId          = $rule->getId();
        $productEntityId = $product->getId();

        $this->connection->delete(
            $this->resource->getTableName('mageplaza_productlabels_rule_meta'),
            [
                $this->connection->quoteInto('rule_id = ?', $ruleId),
                $this->connection->quoteInto('product_id = ?', $productEntityId)
            ]
        );

        $rows = [];
        try {
            $storeIds = $rule->getStoreIds();
            if (!is_array($storeIds)) {
                $storeIds = explode(',', $storeIds);
            }

            $customerGroupIds = $rule->getCustomerGroupIds();
            if (!is_array($customerGroupIds)) {
                $customerGroupIds = explode(',', $customerGroupIds);
            }

            foreach ($storeIds as $storeId) {
                foreach ($customerGroupIds as $customerGroupId) {
                    $rows[] = [
                        'rule_id'           => $ruleId,
                        'product_id'        => $productEntityId,
                        'template_url'      => $rule->getListTemplate(),
                        'img_url'           => $this->helperData->getSrcImg($rule, $storeId),
                        'label'             => $rule->getListLabel(),
                        'label_style'       => $this->helperData->getLabelStyle($rule),
                        'label_fontsize'    => $rule->getListFontSize(),
                        'label_position'    => $rule->getListPosition(),
                        'custom_css'        => $this->helperData->getCategoryCustomCss($rule, $productEntityId),
                        'from_date'         => strtotime($rule->getFromDate()),
                        'to_date'           => strtotime($rule->getToDate()),
                        'customer_group_id' => $customerGroupId,
                        'store_id'          => $storeId,
                        'stop_process'      => $rule->getStopProcess(),
                        'priority'          => $rule->getPriority()
                    ];

                    if (count($rows) === 1000) {
                        $this->connection->insertMultiple($this->getTable('mageplaza_productlabels_rule_meta'), $rows);
                        $rows = [];
                    }
                }
            }

            if (!empty($rows)) {
                $this->connection->insertMultiple(
                    $this->resource->getTableName('mageplaza_productlabels_rule_meta'),
                    $rows
                );
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * @param string $tableName
     *
     * @return string
     */
    protected function getTable($tableName)
    {
        return $this->resource->getTableName($tableName);
    }
}
