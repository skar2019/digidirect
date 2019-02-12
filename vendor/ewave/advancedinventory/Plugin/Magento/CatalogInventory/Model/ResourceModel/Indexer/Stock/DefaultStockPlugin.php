<?php
namespace Ewave\AdvancedInventory\Plugin\Magento\CatalogInventory\Model\ResourceModel\Indexer\Stock;

use Magento\CatalogInventory\Model\ResourceModel\Indexer\Stock\DefaultStock as Subject;
use Magento\CatalogInventory\Model\Indexer\Stock\Action\Full;
use Magento\CatalogInventory\Model\ResourceModel\Indexer\Stock\QueryProcessorComposite;
use Magento\Framework\App\ObjectManager;

class DefaultStockPlugin extends Subject
{
    /**
     * @var \Ewave\AdvancedInventory\Api\StockResolverInterface
     */
    protected $stockResolver;

    /**
     * @var QueryProcessorComposite
     */
    protected $queryProcessorComposite;

    /**
     * @param Subject $subject
     * @param \Closure $proceed
     * @param array $entityIds
     * @return Subject|mixed
     */
    public function aroundReindexEntity(
        Subject $subject,
        \Closure $proceed,
        $entityIds
    ) {
        if (method_exists($subject, 'getActionType') && $subject->getActionType() === 'full') {
            return $proceed($entityIds);
        }
        $this->setTypeId($subject->getTypeId());
        $this->_updateIndex($entityIds);
        return $subject;
    }

    /**
     * Update Stock status index by product ids
     *
     * @param array|int $entityIds
     * @return $this
     */
    protected function _updateIndex($entityIds)
    {
        $connection = $this->getConnection();
        $select = $this->_getStockStatusSelect($entityIds, true);
        $select = $this->getQueryProcessorComposite()->processQuery($select, $entityIds, true);
        $query = $connection->query($select);

        $i = 0;
        $data = [];
        while ($row = $query->fetch(\PDO::FETCH_ASSOC)) {
            $i++;
            $data[] = [
                'product_id' => (int)$row['entity_id'],
                'website_id' => (int)$row['website_id'],
                'stock_id' => (int)$row['stock_id'],
                'qty' => (double)$row['qty'],
                'stock_status' => (int)$row['status'],
            ];
            if ($i % 1000 == 0) {
                $this->_updateIndexTable($data);
                $data = [];
            }
        }

        $this->deleteOldRecords($entityIds);
        $this->_updateIndexTable($data);

        return $this;
    }

    /**
     * Delete records by their ids from index table
     * Used to clean table before re-indexation
     *
     * @param array $ids
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function deleteOldRecords(array $ids)
    {
        if (count($ids) !== 0) {
            $this->getConnection()->delete($this->getMainTable(), [
                'product_id in (?)' => $ids,
            ]);
        }
    }

    /**
     * @return QueryProcessorComposite
     */
    protected function getQueryProcessorComposite()
    {
        if (null === $this->queryProcessorComposite) {
            $this->queryProcessorComposite = ObjectManager::getInstance()
                ->get(QueryProcessorComposite::class);
        }
        return $this->queryProcessorComposite;
    }
}
