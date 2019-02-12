<?php
namespace Ewave\AdvancedInventory\Controller\Adminhtml;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

abstract class AdvancedInventory extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Ewave_AdvancedInventory::advancedinventory_view';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Ewave\AdvancedInventory\Api\AdvancedInventoryRepositoryInterface
     */
    protected $advancedInventoryRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory
     */
    protected $stockItemFactory;

    /**
     * @var \Magento\CatalogInventory\Api\StockItemRepositoryInterface
     */
    protected $stockItemRepository;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * AdvancedInventory constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Ewave\AdvancedInventory\Api\AdvancedInventoryRepositoryInterface $advancedInventoryRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $stockItemFactory
     * @param \Magento\CatalogInventory\Api\StockItemRepositoryInterface $stockItemRepository
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Ewave\AdvancedInventory\Api\AdvancedInventoryRepositoryInterface $advancedInventoryRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory $stockItemFactory,
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface $stockItemRepository,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->advancedInventoryRepository = $advancedInventoryRepository;
        $this->productRepository = $productRepository;
        $this->stockItemFactory = $stockItemFactory;
        $this->stockItemRepository = $stockItemRepository;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Ewave'), __('Ewave'))
            ->addBreadcrumb(__('Advanced Inventory'), __('Advanced Inventory'));
        return $resultPage;
    }

    /**
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface|DataObject
     * @throws LocalizedException
     */
    protected function initStockItem()
    {
        $productId = $this->getRequest()->getParam(StockItemInterface::PRODUCT_ID);
        $stockId = $this->getRequest()->getParam(StockItemInterface::STOCK_ID);

        if ($stockId && $productId) {
            try {
                $stockItem = $this->advancedInventoryRepository->getStockItem($productId, $stockId);
            } catch (\Exception $e) {
                $stockItem = $this->stockItemFactory->create();
            }
            $this->coreRegistry->register('ewave_stockitem', $stockItem);
        } else {
            throw new LocalizedException(__('Wrong request.'));
        }

        $stockItem->setStockId($stockId);
        $stockItem->setProductId($productId);

        return $stockItem;
    }
}
