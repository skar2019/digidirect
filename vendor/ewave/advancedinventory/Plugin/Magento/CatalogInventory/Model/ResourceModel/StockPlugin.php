<?php
namespace Ewave\AdvancedInventory\Plugin\Magento\CatalogInventory\Model\ResourceModel;

use Ewave\AdvancedInventory\Api\StockResolverInterface;
use Magento\CatalogInventory\Model\ResourceModel\Stock as Subject;

class StockPlugin
{
    /**
     * @var \Ewave\AdvancedInventory\Api\StockResolverInterface
     */
    protected $stockResolver;

    /**
     * @param StockResolverInterface $stockResolver
     */
    public function __construct(
        StockResolverInterface $stockResolver
    ) {
        $this->stockResolver = $stockResolver;
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
     * @param array $items
     * @param int $websiteId
     * @param string $operator
     * @return void
     */
    public function aroundCorrectItemsQty(
        Subject $subject,
        \Closure $proceed,
        array $items,
        $websiteId,
        $operator
    ) {
        if (empty($items)) {
            $proceed($items, $websiteId, $operator);
            return;
        }

        $connection = $subject->getConnection();
        $conditions = [];
        foreach ($items as $productId => $qty) {
            $case = $connection->quoteInto('?', $productId);
            $result = $connection->quoteInto("qty{$operator}?", $qty);
            $conditions[$case] = $result;
        }

        $value = $connection->getCaseSql('product_id', $conditions, 'qty');
        $where = [
            'product_id IN (?)' => array_keys($items),
            'website_id = ?' => $websiteId,
            'stock_id = ?' => $this->getStockId(), // Stock ID Added
        ];

        $connection->beginTransaction();
        $connection->update($subject->getTable('cataloginventory_stock_item'), ['qty' => $value], $where);
        $connection->commit();
    }
}
