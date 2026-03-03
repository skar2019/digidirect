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

use Bss\PreOrder\Model\Attribute\Source\Order as SourceOrder;
use Magento\Catalog\Model\ResourceModel\Product\BaseSelectProcessorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface;
use Magento\InventoryIndexer\Model\StockIndexTableNameResolver;
use Magento\InventorySales\Model\GetProductSalableQty;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Stock
 */
class MinPriceConfigurable
{
    const BSS_C_P_E_I = "bss_c_p_e_i";

    /**
     * @var \Bss\PreOrder\Model\Attribute\Source\Module
     */
    protected $sourceModule;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var mixed
     */
    private $stockIndexTableNameResolver;

    /**
     * @var mixed
     */
    protected $defaultStockProvider;

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
     * MinPriceConfigurable constructor.
     *
     * @param \Bss\PreOrder\Model\Attribute\Source\Module $souceModule
     * @param \Bss\PreOrder\Model\Config $config
     * @param ResourceConnection $resourceConnection
     * @param StoreManagerInterface $storeManager
     * @param GetProductSalableQty $getProductSalableQty
     * @param StockResolverInterface $stockResolver
     * @param DefaultStockProviderInterface $defaultStockProvider
     * @param StockIndexTableNameResolver $stockIndexTableNameResolver
     */
    public function __construct(
        \Bss\PreOrder\Model\Attribute\Source\Module $souceModule,
        \Bss\PreOrder\Model\Config $config,
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        GetProductSalableQty $getProductSalableQty,
        StockResolverInterface $stockResolver,
        DefaultStockProviderInterface $defaultStockProvider,
        StockIndexTableNameResolver $stockIndexTableNameResolver
    ) {
        $this->sourceModule = $souceModule;
        $this->config = $config;
        $this->resourceConnection = $resourceConnection;
        $this->storeManager = $storeManager;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->stockResolver = $stockResolver;
        $this->defaultStockProvider = $defaultStockProvider;
        $this->stockIndexTableNameResolver = $stockIndexTableNameResolver;
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
    private function createGetProductSalableQty()
    {
        return $this->getProductSalableQty;
    }

    /**
     * Create object: Magento\InventorySalesApi\Api\StockResolverInterface
     *
     * @return mixed
     */
    private function createStockResolver()
    {
        return $this->stockResolver;
    }

    /**
     * Create object Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface
     *
     * @return mixed
     */
    private function defaultStockProvider()
    {
        return $this->defaultStockProvider;
    }

    /**
     * GEt stock id current
     *
     * @return int
     */
    public function getStockId()
    {
        try {
            $websiteCode = $this->storeManager->getWebsite()
                ->getCode();
            $stock = $this->createStockResolver()->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode);
            return (int)$stock->getStockId();
        } catch (\Exception $exception) {
            return 1;
        }
    }

    /**
     * Create object Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface
     *
     * @return \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface|mixed
     */
    private function stockIndexTableNameResolver()
    {
        return $this->stockIndexTableNameResolver;
    }

    /**
     * Get stock table by stock id
     *
     * @param int $stockId
     * @return mixed
     */
    public function getStockTable($stockId)
    {
        return $this->stockIndexTableNameResolver()->execute($stockId);
    }

    /**
     * Get stock id by default
     *
     * @return mixed
     */
    public function getDefaultStockProviderId()
    {
        return $this->defaultStockProvider()->getId();
    }

    /**
     * Add query product config preorder
     *
     * @param Select $select
     * @param string $isSalableColumnName
     * @return Select
     * @throws NoSuchEntityException
     */
    public function handlePreOrder($select, $isSalableColumnName)
    {
        $storeId = $this->config->getStoreId();
        $bssCPEICurrent = self::BSS_C_P_E_I . "_" . $storeId;
        $catalogProductEntityIntTable = $this->resourceConnection->getTableName("catalog_product_entity_int");
        $attributeIdPreOrder = $this->sourceModule->getAttributeIdPreOrder();
        $select->joinLeft(
            ['bss_c_p_e_i' => $catalogProductEntityIntTable],
            sprintf(
                "%s.entity_id = %s.entity_id AND %s.attribute_id = %s AND %s.store_id = 0",
                self::BSS_C_P_E_I,
                BaseSelectProcessorInterface::PRODUCT_TABLE_ALIAS,
                self::BSS_C_P_E_I,
                $attributeIdPreOrder,
                self::BSS_C_P_E_I
            ),
            ['store_id', 'value']
        );
        $select->joinLeft(
            [$bssCPEICurrent => $catalogProductEntityIntTable],
            sprintf(
                "%s.entity_id = %s.entity_id AND %s.attribute_id = %s AND %s.store_id = %s",
                $bssCPEICurrent,
                BaseSelectProcessorInterface::PRODUCT_TABLE_ALIAS,
                $bssCPEICurrent,
                $attributeIdPreOrder,
                $bssCPEICurrent,
                $storeId
            ),
            ['store_id_' . $storeId => $bssCPEICurrent . ".store_id", 'value_' . $storeId => $bssCPEICurrent . ".value"]
        );

        $select->where(
            sprintf(
                "(stock.%s = 1 OR (%s.store_id = %s AND (%s.value = %s OR %s.value = %s)) OR (%s.store_id IS NULL  AND %s.store_id = 0 AND (%s.value = %s OR   %s.value = %s)))",
                $isSalableColumnName,
                $bssCPEICurrent,
                $storeId,
                $bssCPEICurrent,
                SourceOrder::ORDER_YES,
                $bssCPEICurrent,
                SourceOrder::ORDER_OUT_OF_STOCK,
                $bssCPEICurrent,
                self::BSS_C_P_E_I,
                self::BSS_C_P_E_I,
                SourceOrder::ORDER_YES,
                self::BSS_C_P_E_I,
                SourceOrder::ORDER_OUT_OF_STOCK
            )
        );
        return $select;
    }

    /**
     * Get stock table default
     *
     * @return string
     */
    public function getStockTableDefault()
    {
        return $this->resourceConnection->getTableName('cataloginventory_stock_status');
    }
}
