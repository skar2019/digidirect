<?php
namespace Digidirect\OutOfStockNotif\Plugin\Magento\ProductAlert\Model;

use Digidirect\OutOfStockNotif\Helper\Data as Helper;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\ProductAlert\Model\ProductSalability;
use Magento\Store\Api\Data\WebsiteInterface;

/**
 * Class ProductSalabilityPlugin
 * @package Digidirect\OutOfStockNotif\Plugin\Magento\ProductAlert\Model
 */
class ProductSalabilityPlugin
{
    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var StockStateInterface
     */
    protected $stockState;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * ProductSalabilityPlugin constructor.
     * @param ModuleManager $moduleManager
     * @param StockStateInterface $stockState
     * @param Helper $helper
     */
    public function __construct(
        ModuleManager $moduleManager,
        StockStateInterface $stockState,
        Helper $helper
    ) {
        $this->moduleManager = $moduleManager;
        $this->stockState = $stockState;
        $this->helper = $helper;
    }

    /**
     * @param ProductSalability $productSalability
     * @param callable $proceed
     * @param ProductInterface $product
     * @param WebsiteInterface $website
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundIsSalable(
        ProductSalability $productSalability,
        callable $proceed,
        ProductInterface $product,
        WebsiteInterface $website
    ): bool {
        if ($this->moduleManager->isEnabled('Magento_InventorySalesApi')) {
            $stock = $this->getStockResolverInstance()->execute($this->getTypeWebsite(), $website->getCode());
            $isSalable = $this->getIsProductSalableInstance()->execute($product->getSku(), (int)$stock->getStockId());
        } else {
            $isSalable = $proceed($product, $website);
        }

        if ($this->helper->isEnabledForBackorder()) {
            $stock = $this->stockState->getStockQty($product->getId(), $product->getStore()->getWebsiteId());

            return $isSalable && $stock;
        }

        return $isSalable;
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
    private function getIsProductSalableInstance()
    {
        return ObjectManager::getInstance()
            ->get('Magento\InventorySalesApi\Api\IsProductSalableInterface');
    }

    /**
     * Solving compatibility problem with 2.2 Magento version
     *
     * @return string
     */
    private function getTypeWebsite()
    {
        //in 2.2 Magento version cannot be used \Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE;
        return 'website';
    }
}
