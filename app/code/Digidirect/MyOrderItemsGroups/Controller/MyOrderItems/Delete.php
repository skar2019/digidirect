<?php

namespace Digidirect\MyOrderItemsGroups\Controller\MyOrderItems;

use Magento\Backend\Model\View\Result\Redirect;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupInterface;
use Digidirect\MyOrderItemsGroups\Controller\MyOrderItemsGroups;
use Digidirect\MyOrderItemsGroups\Model\ItemGroupLinkManagement;

/**
 * Class Rename
 * @package Digidirect\MyOrderItemsGroups\Controller\MyOrderItems
 */
class Delete extends AddItem
{
    /**
     * @return Redirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/index', $this->collectParams([OrderItemGroupInterface::GROUP_ID]));
        $customerId = $this->customerSession->getCustomerId();
        $groupId = $this->getRequest()->getParam(OrderItemGroupInterface::GROUP_ID);

        /** Validate input params */
        if (empty($groupId) || !$this->groupRepository->checkGroupForCustomer($customerId, $groupId)) {
            $this->messageManager->addErrorMessage(__('The group could not be deleted.'));
            return $resultRedirect;
        }

        try {
            $itemGroup = $this->groupRepository->deleteById($groupId);
            $this->linkManagement->deleteLinks($groupId);
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('The group is not deleted.'));
        }
        return $resultRedirect;
    }
}
