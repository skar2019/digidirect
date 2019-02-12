<?php
namespace Ewave\AdvancedInventory\Plugin\Magento\CatalogInventory\Model\ResourceModel\Stock;

use Ewave\AdvancedInventory\Api\StockResolverInterface;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Status as Subject;
use Magento\CatalogInventory\Model\Stock;
use Magento\CatalogInventory\Api\StockConfigurationInterface;

class StatusPlugin
{
    /**
     * @var \Ewave\AdvancedInventory\Api\StockResolverInterface
     */
    protected $stockResolver;

    /**
     * @var StockConfigurationInterface
     */
    protected $stockConfiguration;

    /**
     * @param StockResolverInterface $stockResolver
     * @param StockConfigurationInterface $stockConfiguration
     */
    public function __construct(
        StockResolverInterface $stockResolver,
        StockConfigurationInterface $stockConfiguration
    ) {
        $this->stockResolver = $stockResolver;
        $this->stockConfiguration = $stockConfiguration;
    }

    /**
     * @return int
     */
    protected function getStockId()
    {
        return $this->stockResolver->getCurrentStockId();
    }

    /**
     * @param Subject $subject
     * @param \Closure $proceed
     * @param int $productId
     * @param int $status
     * @param int $qty
     * @param int $websiteId
     * @param int $stockId
     * @return $this
     */
    public function aroundSaveProductStatus(
        Subject $subject,
        \Closure $proceed,
        $productId,
        $status,
        $qty,
        $websiteId,
        $stockId = Stock::DEFAULT_STOCK_ID
    ) {
        $stockId = $this->getStockId();
        $connection = $subject->getConnection();
        $select = $connection->select()->from($subject->getMainTable())
            ->where('product_id = :product_id')
            ->where('website_id = :website_id')
            ->where('stock_id = :stock_id');
        $bind = [':product_id' => $productId, ':website_id' => $websiteId, ':stock_id' => $stockId];
        $row = $connection->fetchRow($select, $bind);
        if ($row) {
            $bind = ['qty' => $qty, 'stock_status' => $status];
            $where = [
                $connection->quoteInto('product_id=?', (int)$row['product_id']),
                $connection->quoteInto('website_id=?', (int)$row['website_id']),
                $connection->quoteInto('stock_id=?', (int)$stockId),
            ];
            $connection->update($subject->getMainTable(), $bind, $where);
        } else {
            $bind = [
                'product_id' => $productId,
                'website_id' => $websiteId,
                'stock_id' => $stockId,
                'qty' => $qty,
                'stock_status' => $status,
            ];
            $connection->insert($subject->getMainTable(), $bind);
        }

        return $this;
    }

    /**
     * @param Subject $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param bool $isFilterInStock
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     */
    public function aroundAddStockDataToCollection(
        Subject $subject,
        \Closure $proceed,
        $collection,
        $isFilterInStock
    ) {
        $websiteId = $this->stockConfiguration->getDefaultScopeId();
        $joinCondition = $subject->getConnection()->quoteInto(
            'e.entity_id = stock_status_index.product_id AND stock_status_index.website_id = ?',
            $websiteId
        );

        $productTypes = $this->stockResolver->getAllowedProductTypes();
        $joinCondition .= $subject->getConnection()->quoteInto(
            ' AND (stock_status_index.stock_id = ? OR e.type_id NOT IN ("' . implode('", "', $productTypes) . '"))',
            $this->getStockId()
        );

        $method = $isFilterInStock ? 'join' : 'joinLeft';
        $collection->getSelect()->$method(
            ['stock_status_index' => $subject->getMainTable()],
            $joinCondition,
            ['is_salable' => 'stock_status']
        );

        if ($isFilterInStock) {
            $collection->getSelect()->where(
                'stock_status_index.stock_status = ?',
                Stock\Status::STATUS_IN_STOCK
            );
        }
        return $collection;
    }

    /**
     * @param Subject $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return $this
     */
    public function aroundAddIsInStockFilterToCollection(
        Subject $subject,
        \Closure $proceed,
        $collection
    ) {
        $websiteId = $this->stockConfiguration->getDefaultScopeId();
        $joinCondition = $subject->getConnection()->quoteInto(
            'e.entity_id = stock_status_index.product_id AND stock_status_index.website_id = ?',
            $websiteId
        );

        $productTypes = $this->stockResolver->getAllowedProductTypes();
        $joinCondition .= $subject->getConnection()->quoteInto(
            ' AND (stock_status_index.stock_id = ? OR e.type_id NOT IN ("' . implode('", "', $productTypes) . '"))',
            $this->getStockId()
        );

        $collection->getSelect()->join(
            ['stock_status_index' => $subject->getMainTable()],
            $joinCondition,
            []
        )->where(
            'stock_status_index.stock_status=?',
            Stock\Status::STATUS_IN_STOCK
        );
        return $this;
    }
}
