<?php

namespace Digidirect\Navigation\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Digidirect\Navigation\Api\MenuRepositoryInterface;
use Magento\Store\Model\Store;

/**
 * Class Delete
 * @package Digidirect\Navigation\Controller\Adminhtml\Set
 */
class Delete extends Action
{
    /**
     * @var MenuRepositoryInterface
     */
    protected $menuRepository;

    /**
     * Delete constructor.
     * @param Context $context
     * @param MenuRepositoryInterface $menuRepository
     */
    public function __construct(
        Action\Context $context,
        MenuRepositoryInterface $menuRepository
    ) {
        parent::__construct($context);
        $this->menuRepository = $menuRepository;
    }

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        $storeId = (int)$this->getRequest()->getParam('store', Store::DEFAULT_STORE_ID);
        if ($id) {
            try {
                if ($storeId == Store::DEFAULT_STORE_ID) {
                    $this->menuRepository->deleteById($id);
                    $this->messageManager->addSuccessMessage(__('You deleted the menu item.'));
                } else {
                    $this->menuRepository->deleteByIdAndStoreId($id, $storeId);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a menu item to delete.'));
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Check if Is allowed to delete
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Digidirect_Navigation::navigation_menu_items_delete');
    }
}
