<?php

namespace Digidirect\MyOrderItemsGroups\Controller\MyOrderItems;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupInterface;
use Digidirect\MyOrderItemsGroups\Controller\MyOrderItemsGroups;

/**
 * Class AddGroup
 * @package Digidirect\MyOrderItemsGroups\Controller\MyOrderItems
 */
class AddGroup extends MyOrderItemsGroups
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

        $itemGroup = $this->orderItemGroupFactory->create();
        $itemGroup->setCustomerId($this->customerSession->getCustomerId());
        $itemGroup->setName($this->configHelper->getDefaultGroupName());
        $this->dataHelper->setUpdatedAt($itemGroup);

        try {
            $this->groupRepository->save($itemGroup);
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage(__('The group could not be created.'));
        }

        return $resultRedirect;
    }
}
