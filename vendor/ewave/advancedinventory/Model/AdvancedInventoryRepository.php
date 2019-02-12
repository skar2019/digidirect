<?php
namespace Ewave\AdvancedInventory\Model;

use Ewave\AdvancedInventory\Api\AdvancedInventoryRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class AdvancedInventoryRepository implements AdvancedInventoryRepositoryInterface
{
    /**
     * @var \Ewave\AdvancedInventory\Model\ResourceModel\AdvancedInventoryStock
     */
    protected $resourceStock;

    /**
     * @var \Ewave\AdvancedInventory\Api\StockResolverInterface
     */
    protected $stockResolver;

    /**
     * @var \Ewave\AbstractEntity\Api\AbstractEntityRepositoryInterface
     */
    protected $abstractEntityRepository;

    /**
     * @var \Magento\CatalogInventory\Api\StockItemRepositoryInterface
     */
    protected $stockItemRepository;

    /**
     * @var \Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory
     */
    protected $stockItemCriteriaFactory;

    /**
     * @var \Magento\CatalogInventory\Api\StockRepositoryInterface
     */
    protected $stockRepository;

    /**
     * @param \Ewave\AdvancedInventory\Model\ResourceModel\AdvancedInventoryStock $resourceStock
     * @param \Ewave\AdvancedInventory\Api\StockResolverInterface $stockResolver
     * @param \Ewave\AbstractEntity\Api\AbstractEntityRepositoryInterface $abstractEntityRepository
     * @param \Magento\CatalogInventory\Api\StockItemRepositoryInterface $stockItemRepository
     * @param \Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory $stockItemCriteriaFactory
     * @param \Magento\CatalogInventory\Api\StockRepositoryInterface $stockRepository
     */
    public function __construct(
        \Ewave\AdvancedInventory\Model\ResourceModel\AdvancedInventoryStock $resourceStock,
        \Ewave\AdvancedInventory\Api\StockResolverInterface $stockResolver,
        \Ewave\AbstractEntity\Api\AbstractEntityRepositoryInterface $abstractEntityRepository,
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface $stockItemRepository,
        \Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory $stockItemCriteriaFactory,
        \Magento\CatalogInventory\Api\StockRepositoryInterface $stockRepository
    ) {
        $this->resourceStock = $resourceStock;
        $this->stockResolver = $stockResolver;
        $this->abstractEntityRepository = $abstractEntityRepository;
        $this->stockItemRepository = $stockItemRepository;
        $this->stockItemCriteriaFactory = $stockItemCriteriaFactory;
        $this->stockRepository = $stockRepository;
    }

    /**
     * @return int
     */
    protected function getCurrentStockId()
    {
        return $this->stockResolver->getCurrentStockId();
    }

    /**
     * {@inheritdoc}
     */
    public function getStockItem($productId, $stockId = null, $scopeId = null)
    {
        if ($stockId === null) {
            $stockId = $this->getCurrentStockId();
        }

        /** @var \Magento\CatalogInventory\Api\StockItemCriteriaInterface $stockItemCriteria */
        $stockItemCriteria = $this->stockItemCriteriaFactory->create();
        $stock = $this->stockRepository->get($stockId);
        $stockItemCriteria->setStockFilter($stock);
        $stockItemCriteria->setProductsFilter($productId);
        if ($scopeId !== null) {
            $stockItemCriteria->setScopeFilter($scopeId);
        }

        $stockItems = $this->stockItemRepository->getList($stockItemCriteria)->getItems();
        if (!empty($stockItems)) {
            return current($stockItems);
        }

        throw new LocalizedException(__('Stock Item does not exist.'));
    }

    /**
     * {@inheritdoc}
     */
    public function getAbstractEntity($stockId = null)
    {
        if ($stockId === null) {
            $stockId = $this->getCurrentStockId();
        }

        $entityId = $this->resourceStock->getEntityIdByStockId($stockId);
        return $this->abstractEntityRepository->getById($entityId);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalStockQuantity($productId, $addDefaultStock = false)
    {
        return $this->resourceStock->getTotalStockQuantity($productId, $addDefaultStock);
    }
}
