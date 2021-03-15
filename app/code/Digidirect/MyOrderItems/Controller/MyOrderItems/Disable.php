<?php

namespace Digidirect\MyOrderItems\Controller\MyOrderItems;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Digidirect\MyOrderItems\Model\OrderItemStateRepository;
use Digidirect\MyOrderItems\Controller\MyOrderItems;

/**
 * Class Disable
 * @package Digidirect\MyOrderItems\Controller\MyOrderItems
 */
class Disable extends Enable
{
    /**
     * Show my order items
     *
     * @return \Magento\Framework\View\Result\Page
     * @throws NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam(self::ITEM_ID);
        $customerId = $this->customerSession->getCustomerId();
        try {
            if ($this->stateRepository->checkItemPermission($id, $customerId)) {
                $this->stateRepository->disableOrderItem($id, $this->customerSession->getCustomerId());
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(__('Item has not been disabled.'));
        }
        return $resultRedirect->setPath('*/*/index', $this->collectParams());
    }
}
