<?php

namespace Digidirect\MyOrderItemsGroups\Controller\MyOrderItems;

use Magento\Backend\Model\View\Result\Redirect;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupInterface;
use Digidirect\MyOrderItemsGroups\Controller\MyOrderItemsGroups;

/**
 * Class Rename
 * @package Digidirect\MyOrderItemsGroups\Controller\MyOrderItems
 */
class Rename extends MyOrderItemsGroups
{
    /**
     * @return Redirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/index', $this->collectParams());
        $customerId = $this->customerSession->getCustomerId();
        $groupId = $this->getRequest()->getParam(OrderItemGroupInterface::GROUP_ID);
        $name = $this->getRequest()->getParam(OrderItemGroupInterface::NAME);

        /** Validate input params */
        if (empty($name) || empty($groupId) || !$this->groupRepository->checkGroupForCustomer($customerId, $groupId)) {
            $this->messageManager->addErrorMessage(__('The group could not be renamed.'));
            return $resultRedirect;
        }

        try {
            $itemGroup = $this->groupRepository->getById($groupId);
            $itemGroup->setName($name);
            $this->dataHelper->setUpdatedAt($itemGroup);
            $this->groupRepository->save($itemGroup);
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('The group is not saved.'));
        }
        return $resultRedirect;
    }
}
