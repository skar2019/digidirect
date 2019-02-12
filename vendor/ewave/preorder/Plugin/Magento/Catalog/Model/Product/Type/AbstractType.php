<?php
namespace Ewave\PreOrder\Plugin\Magento\Catalog\Model\Product\Type;

/**
 * Class AbstractType
 * @package Ewave\PreOrder\Plugin\Magento\Catalog\Model\Product\Type
 */
class AbstractType
{
    /**
     * @var \Magento\CatalogInventory\Model\Stock\StockItemRepository
     */
    protected $stockItemRepository;

    /**
     * @var \Ewave\PreOrder\Helper\Data
     */
    protected $preOrderHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * AbstractType constructor.
     * @param \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository
     * @param \Ewave\PreOrder\Helper\Data $preOrderHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Ewave\PreOrder\Helper\Data $preOrderHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->stockItemRepository = $stockItemRepository;
        $this->preOrderHelper = $preOrderHelper;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Catalog\Model\Product\Type\AbstractType $subject
     * @param callable $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function aroundIsSalable(
        \Magento\Catalog\Model\Product\Type\AbstractType $subject,
        callable $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        $result = $proceed($product);
        if (!$result) {
            try {
                $stockItem = $this->stockItemRepository->get($product->getId());
                $result = $this->preOrderHelper->checkStockItemQty($stockItem);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
        return $result;
    }
}
