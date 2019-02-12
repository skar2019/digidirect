<?php
namespace Ewave\AdvancedInventory\Model\ResourceModel\AbstractEntity\Relation\Stock;

use Ewave\AdvancedInventory\Api\Data\AdvancedInventoryStockInterface;
use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Ewave\AdvancedInventory\Helper\Config as ConfigHelper;
use Ewave\AdvancedInventory\Model\ResourceModel\AdvancedInventoryStock;
use Magento\CatalogInventory\Api\Data\StockInterfaceFactory;
use Magento\CatalogInventory\Api\StockRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class SaveHandler
 */
abstract class AbstractHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var AdvancedInventoryStock
     */
    protected $advancedInventoryStock;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @var StockInterfaceFactory
     */
    protected $stockFactory;

    /**
     * @var StockRepositoryInterface
     */
    protected $stockRepository;

    /**
     * @param MetadataPool $metadataPool
     * @param AdvancedInventoryStock $advancedInventoryStock
     * @param ConfigHelper $configHelper
     * @param StockInterfaceFactory $stockFactory
     * @param StockRepositoryInterface $stockRepository
     */
    public function __construct(
        MetadataPool $metadataPool,
        AdvancedInventoryStock $advancedInventoryStock,
        ConfigHelper $configHelper,
        StockInterfaceFactory $stockFactory,
        StockRepositoryInterface $stockRepository
    ) {
        $this->metadataPool = $metadataPool;
        $this->advancedInventoryStock = $advancedInventoryStock;
        $this->configHelper = $configHelper;
        $this->stockFactory = $stockFactory;
        $this->stockRepository = $stockRepository;
    }
}
