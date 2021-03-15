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
class Clear extends AddItem
{
    /**
     * Const sales item ids param
     */
    const SALES_ITEM_IDS_PARAM = 'sales_item_ids';

    /**
     * @return Redirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/index', $this->collectParams());
        $customerId = $this->customerSession->getCustomerId();
        $itemIds = $this->getRequest()->getParam(self::SALES_ITEM_IDS_PARAM);
        $groupId = $this->getRequest()->getParam(OrderItemGroupInterface::GROUP_ID);

        /** Validate input params */
        if (empty($itemIds) || empty($groupId) || !$this->groupRepository->checkGroupForCustomer($customerId, $groupId)) {
            $this->messageManager->addErrorMessage(__('The group items could not be cleared.'));
            return $resultRedirect;
        }

        if (is_string($itemIds)) {
            $itemIds = $this->dataHelper->jsonDecode($itemIds);
        }

        try {
            $this->linkManagement->deleteLinks($groupId, $itemIds);
            $itemGroup = $this->groupRepository->getById($groupId);
            $this->dataHelper->setUpdatedAt($itemGroup);
            $this->groupRepository->save($itemGroup);
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('The group items is not cleared.'));
        }
        return $resultRedirect;
    }
}
