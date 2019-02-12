<?php
namespace Ewave\AdvancedInventory\Block\Adminhtml\Order;

/**
 * Class View
 *
 * @package Ewave\AdvancedInventory\Block\Adminhtml\Order
 */
class View extends \Magento\Backend\Block\Widget
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\CatalogInventory\Api\StockRepositoryInterface
     */
    protected $stockRepository;

    /**
     * @var \Magento\CatalogInventory\Api\Data\StockInterface
     */
    protected $stock;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\CatalogInventory\Api\StockRepositoryInterface $stockRepository
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\CatalogInventory\Api\StockRepositoryInterface $stockRepository,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->stockRepository = $stockRepository;
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('sales_order');
    }

    /**
     * @return bool|\Magento\CatalogInventory\Api\Data\StockInterface
     */
    public function getStock()
    {
        if (null === $this->stock) {
            try {
                $this->stock = $this->stockRepository->get($this->getOrder()->getStockId());
            } catch (\Exception $e) {
                //stock doesn't exist
                $this->stock = false;
            }
        }
        return $this->stock;
    }

    /**
     * @return string
     */
    public function getStockName()
    {
        if ($this->getStock()) {
            return $this->getStock()->getStockName();
        }
        return '';
    }
}
