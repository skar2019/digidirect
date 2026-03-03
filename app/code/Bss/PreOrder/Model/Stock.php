<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_PreOrder
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2022 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\PreOrder\Model;

use Magento\InventorySales\Model\GetProductSalableQty;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Stock
 */
class Stock
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var mixed
     */
    protected $getProductSalableQty;

    /**
     * @var mixed
     */
    protected $stockResolver;

    /**
     * Construct
     *
     * @param StoreManagerInterface $storeManager
     * @param GetProductSalableQty $getProductSalableQty
     * @param StockResolverInterface $stockResolver
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        GetProductSalableQty $getProductSalableQty,
        StockResolverInterface $stockResolver
    ) {
        $this->storeManager = $storeManager;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->stockResolver = $stockResolver;
    }

    /**
     * Get salable qty by stock current website
     *
     * @param string $sku
     * @return int|float
     */
    public function getProductSalableQty($sku)
    {
        try {
            $websiteCode = $this->storeManager->getWebsite()->getCode();
            $stock = $this->createStockResolver()->execute(\Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE, $websiteCode);
            $stockId = $stock->getStockId();
            return $this->createGetProductSalableQty()->execute($sku, $stockId);
        } catch (\Exception $exception) {
            return 0;
        }
    }

    /**
     * Create object Magento\InventorySales\Model\GetProductSalableQty
     *
     * @return mixed
     */
    public function createGetProductSalableQty()
    {
        return $this->getProductSalableQty;
    }

    /**
     * Create object: Magento\InventorySalesApi\Api\StockResolverInterface
     *
     * @return mixed
     */
    public function createStockResolver()
    {
        return $this->stockResolver;
    }
}
