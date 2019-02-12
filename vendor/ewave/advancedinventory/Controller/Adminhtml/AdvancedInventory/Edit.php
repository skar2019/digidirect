<?php
namespace Ewave\AdvancedInventory\Controller\Adminhtml\AdvancedInventory;

use Ewave\AdvancedInventory\Controller\Adminhtml\AdvancedInventory as AdvancedInventoryController;
use Magento\Framework\Exception\LocalizedException;

class Edit extends AdvancedInventoryController
{
    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $stockItem = $this->initStockItem();
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('/');
        }

        $abstractEntity = $this->advancedInventoryRepository->getAbstractEntity($stockItem->getStockId());
        $product = $this->productRepository->getById($stockItem->getProductId(), false, 0);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            __('Manage Stock'),
            __('Manage Stock')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Advanced Inventory'));
        $resultPage->getConfig()->getTitle()->prepend($abstractEntity->getName() . ' / ' . $product->getName());
        return $resultPage;
    }
}
