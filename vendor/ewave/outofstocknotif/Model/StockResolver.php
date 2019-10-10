<?php
namespace Ewave\OutOfStockNotif\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Class StockResolver
 * @package Ewave\OutOfStockNotif\Model
 */
class StockResolver
{
    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * StockResolver constructor.
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        ModuleManager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param $product
     * @return mixed
     */
    public function getProductStockItem($product)
    {
        if ($this->moduleManager->isEnabled('Magento_InventorySalesApi')
            && $this->moduleManager->isEnabled('Magento_InventoryConfigurationApi')) {
            $websiteCode = $product->getStore()->getWebsite()->getCode();
            $stock = $this->getStockResolverInstance()->execute(
                'website',
                $websiteCode
            );
            $qty = $this->getProductSalableQtyInterface()->execute(
                $product->getSku(),
                $stock->getId()
            );
            $stockItem = $this->getStockItemConfigurationInterface()->execute(
                $product->getSku(),
                $stock->getId()
            );
            $stockItem->setData('qty', $qty);
        } else {
            $stockItem = $product->getExtensionAttributes()->getStockItem();
            if (!$stockItem) {
                $stockItem = $this->getStockRegistry()->getStockItem($product->getId());
            }
        }

        return $stockItem;
    }

    /**
     * Solving compatibility problem with 2.2 Magento version
     *
     * @return object
     */
    private function getStockResolverInstance()
    {
        return ObjectManager::getInstance()
            ->get('Magento\InventorySalesApi\Api\StockResolverInterface');
    }

    /**
     * Solving compatibility problem with 2.2 Magento version
     *
     * @return object
     */
    private function getProductSalableQtyInterface()
    {
        return ObjectManager::getInstance()
            ->get('Magento\InventorySalesApi\Api\GetProductSalableQtyInterface');
    }

    /**
     * Solving compatibility problem with 2.2 Magento version
     *
     * @return object
     */
    private function getStockItemConfigurationInterface()
    {
        return ObjectManager::getInstance()
            ->get('Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface');
    }

    /**
     * Solving compatibility problem
     *
     * @return mixed
     */
    private function getStockRegistry()
    {
        return ObjectManager::getInstance()
            ->get('Magento\CatalogInventory\Api\StockRegistryInterface');
    }
}
