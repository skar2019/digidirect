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

namespace Mageplaza\ProductLabels\Model\Indexer;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Indexer\CacheContext;
use Magento\Framework\Mview\ActionInterface;
use Mageplaza\ProductLabels\Helper\Data;
use Mageplaza\ProductLabels\Model\Indexer\Rule\Action\Full;
use Mageplaza\ProductLabels\Model\Indexer\Rule\Action\Row;
use Mageplaza\ProductLabels\Model\Indexer\Rule\Action\Rows;
use Mageplaza\ProductLabels\Model\ResourceModel\Rule;
use Mageplaza\ProductLabels\Model\ResourceModel\Rule\CollectionFactory;

/**
 * Class RuleIndexer
 * @package Mageplaza\ProductLabels\Model\Indexer
 */
class RuleIndexer implements \Magento\Framework\Indexer\ActionInterface, ActionInterface
{
    /**
     * @var CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var Full
     */
    protected $reindexRuleLabelFull;

    /**
     * @var Row
     */
    protected $reindexRuleLabelRow;

    /**
     * @var Rows
     */
    protected $reindexRuleLabelRows;

    /**
     * @var CacheContext
     */
    private $cacheContext;

    /**
     * RuleIndexer constructor.
     *
     * @param CollectionFactory $ruleCollectionFactory
     * @param Data $helperData
     * @param ResourceConnection $resource
     * @param Full $reindexRuleLabelFull
     * @param Row $reindexRuleLabelRow
     * @param Rows $reindexRuleLabelRows
     */
    public function __construct(
        CollectionFactory $ruleCollectionFactory,
        Data $helperData,
        ResourceConnection $resource,
        Full $reindexRuleLabelFull,
        Row $reindexRuleLabelRow,
        Rows $reindexRuleLabelRows
    ) {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->helperData            = $helperData;
        $this->resource              = $resource;
        $this->connection            = $resource->getConnection();
        $this->reindexRuleLabelFull  = $reindexRuleLabelFull;
        $this->reindexRuleLabelRow   = $reindexRuleLabelRow;
        $this->reindexRuleLabelRows  = $reindexRuleLabelRows;
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->connection->truncateTable(
            $this->getTable('mageplaza_productlabels_rule_meta')
        );

        /** @var \Mageplaza\ProductLabels\Model\Rule $rule */
        foreach ($this->getAllRules() as $rule) {
            $this->reindexRuleLabelFull->execute($rule, 1000);
        }

        $this->getCacheContext()->registerTags(
            [
                Category::CACHE_TAG,
                Product::CACHE_TAG
            ]
        );
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     *
     * @throws InputException
     * @throws LocalizedException
     */
    public function execute($ids)
    {
        $this->reindexRuleLabelRows->execute($ids);
        $this->getCacheContext()->registerEntities(Product::CACHE_TAG, $ids);
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param array $ids
     *
     * @throws InputException
     * @throws LocalizedException
     */
    public function executeList(array $ids)
    {
        $this->reindexRuleLabelRows->execute($ids);
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     *
     * @throws InputException
     * @throws LocalizedException
     */
    public function executeRow($id)
    {
        $this->reindexRuleLabelRow->execute($id);
    }

    /**
     * Get cache context
     *
     * @return CacheContext
     * @deprecated 100.0.11
     */
    protected function getCacheContext()
    {
        if (!($this->cacheContext instanceof CacheContext)) {
            return ObjectManager::getInstance()->get(CacheContext::class);
        }

        return $this->cacheContext;
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

    /**
     * Get active rules
     *
     * @return Rule\Collection
     */
    protected function getAllRules()
    {
        return $this->ruleCollectionFactory->create()->setOrder('priority', 'ASC');
    }
}
