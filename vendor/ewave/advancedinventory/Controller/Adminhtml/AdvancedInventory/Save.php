<?php
namespace Ewave\AdvancedInventory\Controller\Adminhtml\AdvancedInventory;

use Ewave\AdvancedInventory\Controller\Adminhtml\AdvancedInventory as AdvancedInventoryController;
use Ewave\AbstractEntity\Api\Data\AbstractEntityInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;

class Save extends AdvancedInventoryController
{
    const ADMIN_RESOURCE = 'Ewave_AdvancedInventory::advancedinventory_edit';

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $stockData = $this->getRequest()->getParams();
        try {
            $stockItem = $this->initStockItem();
            $stockItem->addData($stockData);
            if (!$stockItem->getItemId()) {
                $stockItem->setItemId(null);
            }
            $this->stockItemRepository->save($stockItem);
            $this->messageManager->addSuccessMessage(__('Warehouse stock data has been updated.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('/');
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $back = $this->getRequest()->getParam('back');
        $backTo = $this->getRequest()->getParam('back_to');
        if ($back == 'edit') {
            return $resultRedirect->setPath('*/*/edit', [
                StockItemInterface::STOCK_ID => $stockItem->getStockId(),
                StockItemInterface::PRODUCT_ID => $stockItem->getProductId(),
                StockItemInterface::ITEM_ID => $stockItem->getItemId(),
                'back_to' => $backTo
            ]);
        }

        if ($backTo == 'stock') {
            $abstractEntity = $this->advancedInventoryRepository->getAbstractEntity($stockItem->getStockId());
            return $resultRedirect->setPath('ewave_abstractentity/abstractentity/edit', [
                'id' => $abstractEntity->getId(),
                AbstractEntityInterface::ATTRIBUTE_SET_ID => $abstractEntity->getAttributeSetId(),
            ]);
        }

        return $resultRedirect->setPath('catalog/product/edit', ['id' => $stockItem->getProductId()]);
    }
}
