<?php
namespace Ewave\AdvancedInventory\Model\ResourceModel\Stock\Item;

use Ewave\AdvancedInventory\Api\StockResolverInterface;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Item\StockItemCriteriaMapper as Mapper;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\ObjectFactory;
use Magento\Framework\DB\MapperFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DB\Select;
use Psr\Log\LoggerInterface as Logger;

class StockItemCriteriaMapper extends Mapper
{
    /**
     * @var \Ewave\AdvancedInventory\Api\StockResolverInterface
     */
    protected $stockResolver;

    /**
     * StockStatusCriteriaMapper constructor.
     * @param Logger $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ObjectFactory $objectFactory
     * @param MapperFactory $mapperFactory
     * @param StoreManagerInterface $storeManager
     * @param StockResolverInterface $stockResolver
     * @param Select|null $select
     */
    public function __construct(
        Logger $logger,
        FetchStrategyInterface $fetchStrategy,
        ObjectFactory $objectFactory,
        MapperFactory $mapperFactory,
        StoreManagerInterface $storeManager,
        StockResolverInterface $stockResolver,
        Select $select = null
    ) {
        parent::__construct(
            $logger,
            $fetchStrategy,
            $objectFactory,
            $mapperFactory,
            $storeManager,
            $select
        );
        $this->stockResolver = $stockResolver;
    }

    /**
     * Apply initial query parameters
     *
     * @return void
     */
    public function mapInitialCondition()
    {
        parent::mapInitialCondition();
        $stockId = $this->stockResolver->getCurrentStockId();
        $productTypes = $this->stockResolver->getAllowedProductTypes();
        $this->getSelect()->where(
            'main_table.stock_id = ? OR cp_table.type_id NOT IN ("' . implode('", "', $productTypes) . '")',
            $stockId
        );
    }
}
