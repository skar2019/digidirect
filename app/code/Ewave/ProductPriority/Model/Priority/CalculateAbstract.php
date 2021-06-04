<?php
namespace Ewave\ProductPriority\Model\Priority;

/**
 * Class CalculateAbstract
 * @package Ewave\ProductPriority\Model\Priority
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class CalculateAbstract extends \Magento\Framework\DataObject
{
    const STOCK_WEBSITE_ID = 0;
    const STOCK_STOCK_ID = 0;

    /**
     * @var \Ewave\ProductPriority\Helper\Config
     */
    protected $_priorityConfigHelper;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    protected $_stockItemRepository;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $_indexerFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var array
     */
    protected $_reIndexTypes;

    /**
     * @var \Ewave\ProductPriority\Helper\Config
     */
    protected $_eavConfig;

    /**
     * @var array
     */
    protected $_categoriesProducts;

    /**
     * CalculateAbstract constructor.
     * @param \Ewave\ProductPriority\Helper\Config $config
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Indexer\Model\IndexerFactory $indexerFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param array $reIndexTypes
     */
    public function __construct(
        \Ewave\ProductPriority\Helper\Config $config,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        $reIndexTypes = []
    ) {
        parent::__construct();
        $this->_priorityConfigHelper = $config;
        $this->_stockItemRepository = $stockItemRepository;
        $this->_resource = $resource;
        $this->_connection = $this->_resource->getConnection();
        $this->_indexerFactory = $indexerFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_reIndexTypes = $reIndexTypes;
        $this->_eavConfig = $catalogConfig;
    }

    /**
     * Get orders
     * @param array $orderIds
     * @return int[]
     */
    abstract protected function _getPrioritySortItems(array $orderIds);

    /**
     * @return $this
     */
    public function calculate()
    {
        $reindex = $this->_orderedSort();
        if ($reindex) {
            $this->_reindex();
        }
        return $this;
    }

    /**
     * @return bool
     */
    protected function _orderedSort()
    {
        /** Get all new products */
        $newProducts = $this->_findNewProducts();

        /** Get all in stock products */
        $inStockProducts = $this->_findInStockProducts();

        /** Get all orders for period */
        $orderIds = $this->_getOrdersForPeriod();
        $prioritySort = [];
        if (!empty($orderIds)) {
            $prioritySort = $this->_getPrioritySortItems($orderIds);
        }

        $updateData = [];
        $categories = $this->_getCategories();
        foreach ($categories as $categoryId) {
            $products = $this->_getCategoriesProducts($categoryId);
            $products = $this->_sortCategoryProducts($products, $newProducts, $inStockProducts, $prioritySort);

            $pos = 0;
            $updateData = array_merge(
                $updateData,
                array_map(function ($productId) use ($categoryId, &$pos) {
                    return [
                        'category_id' => $categoryId,
                        'product_id'  => $productId,
                        'position'    => ++$pos,
                    ];
                }, $products)
            );
        }

        if (!empty($updateData)) {
            $this->_updateProductsPositions($updateData);
            return true;
        }

        return false;
    }

    /**
     * Get categories
     * @return int[]
     */
    protected function _getCategories()
    {
        $categories = $this->_connection->select()
            ->from(
                $this->_resource->getTableName('catalog_category_product'),
                ['category_id']
            )
            ->group('category_id');
        return array_map('intval', $this->_connection->fetchCol($categories));
    }

    /**
     * Get new products
     * @return int[]
     */
    protected function _findNewProducts()
    {
        if (!$this->_priorityConfigHelper->isPushNewProductToTop()) {
            return [];
        }

        $productTable = $this->_resource->getTableName('catalog_product_entity');

        $newProductPeriod = new \DateTime('-' . $this->_priorityConfigHelper->getPeriodForNewProducts() . 'day');
        $newProductPeriod->setTime('00', '00', '01');

        $select = $this->_connection->select()
            ->from(
                ['main_table' => $productTable],
                ['entity_id']
            )
            ->where('created_at >= ?', $newProductPeriod->format('Y-m-d H:i:s'))
            ->order('created_at DESC');
        return array_flip($this->_connection->fetchCol($select));
    }

    /**
     * Get in stock products
     * @return int[]
     */
    protected function _findInStockProducts()
    {
        if (!$this->_priorityConfigHelper->isPushOutOfStockInBottom()) {
            return [];
        }

        $select = $this->_connection->select()
            ->from($this->_resource->getTableName('cataloginventory_stock_status'), 'product_id')
            ->where('stock_status = 1');
        return array_flip($this->_connection->fetchCol($select));
    }

    /**
     * Get products of category
     * @param int $category
     * @return array|false
     */
    protected function _getCategoriesProducts(int $category)
    {
        if (null === $this->_categoriesProducts) {
            $select = $this->_connection->select()
                ->from($this->_resource->getTableName('catalog_category_product'), ['category_id', 'product_id']);
            $categoriesProducts = $this->_connection->fetchAll($select);

            foreach ($categoriesProducts as $row) {
                $this->_categoriesProducts[$row['category_id']][] = (int)$row['product_id'];
            }
        }

        return $this->_categoriesProducts[$category] ?? false;
    }

    /**
     * Get orders for the period
     * @return int[]
     */
    protected function _getOrdersForPeriod()
    {
        $date = new \DateTime('-' . $this->_priorityConfigHelper->getSortByPeriod() . 'day');
        $ordersSelect = $this->_connection->select()
            ->from($this->_resource->getTableName('sales_order'), ['entity_id'])
            ->where('created_at >= ?', $date->format('Y-m-d'))
            ->where('status = ?', \Magento\Sales\Model\Order::STATE_COMPLETE);

        return array_map('intval', $this->_connection->fetchCol($ordersSelect));
    }

    /**
     * Sort category products
     * @param array $products
     * @param array $newProducts
     * @param array $inStockProducts
     * @param array $prioritySort
     * @return array
     */
    protected function _sortCategoryProducts(
        array $products,
        array $newProducts,
        array $inStockProducts,
        array $prioritySort
    ) {
        usort($products, function ($a, $b) use ($newProducts, $inStockProducts, $prioritySort) {
            $pointsA = $pointsB = 0;
            if (isset($inStockProducts[$a])) {
                $pointsA += 2;
            }
            if (isset($inStockProducts[$b])) {
                $pointsB += 2;
            }
            if (isset($newProducts[$a])) {
                ++$pointsA;
            }
            if (isset($newProducts[$b])) {
                ++$pointsB;
            }
            if ($pointsA == $pointsB) {
                $pointsA = $prioritySort[$a] ?? 0;
                $pointsB = $prioritySort[$b] ?? 0;
                if ($pointsA == $pointsB) {
                    return $a < $b ? -1 : 1;
                } else {
                    return $pointsA > $pointsB ? -1 : 1;
                }
            }
            return $pointsA > $pointsB ? -1 : 1;
        });

        return $products;
    }

    /**
     * Update categories products positions
     * @param array $updateData
     * @return void
     */
    protected function _updateProductsPositions(array $updateData)
    {
        $this->_connection->insertOnDuplicate(
            $this->_resource->getTableName('catalog_category_product'),
            $updateData,
            ['category_id', 'product_id', 'position']
        );
    }

    /**
     * Re-index after success sort logic
     * @return $this
     */
    protected function _reindex()
    {
        foreach ($this->_reIndexTypes as $process) {
            $indexer = $this->_indexerFactory->create();
            $indexer->load($process);
            $indexer->reindexAll();
        }
        return $this;
    }
}
