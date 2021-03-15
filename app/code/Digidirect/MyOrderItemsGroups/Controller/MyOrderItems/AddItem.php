<?php

namespace Digidirect\MyOrderItemsGroups\Controller\MyOrderItems;

use Digidirect\MyOrderItems\Model\UrlHandlerPool;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupInterface;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupLinkInterface;
use Digidirect\MyOrderItemsGroups\Model\OrderItemGroupRepository;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupInterfaceFactory;
use Digidirect\MyOrderItemsGroups\Controller\MyOrderItemsGroups;
use Digidirect\MyOrderItemsGroups\Helper\Data as DataHelper;
use Digidirect\MyOrderItemsGroups\Helper\Config as ConfigHelper;
use Digidirect\MyOrderItemsGroups\Model\ItemGroupLinkManagement;

/**
 * Class Rename
 * @package Digidirect\MyOrderItemsGroups\Controller\MyOrderItems
 */
class AddItem extends MyOrderItemsGroups
{
    /**
     * Constants
     */
    const MYORDERITEMS_INDEX_LAYOUT = 'myorderitems_myorderitems_index';
    const ORDER_ITEMS_GROUPS_BLOCK_NAME = 'my_order_item_group_list';
    const RESPONSE_MESSAGE = 'message';
    const RESPONSE_BODY = 'body';

    /**
     * @var ItemGroupLinkManagement
     */
    protected $linkManagement;

    /**
     * Add constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param OrderItemGroupRepository $groupRepository
     * @param OrderItemGroupInterfaceFactory $orderItemGroupFactory
     * @param UrlHandlerPool $urlHandlerPool
     * @param DataHelper $dataHelper
     * @param ConfigHelper $configHelper
     * @param ItemGroupLinkManagement $linkManagement
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        OrderItemGroupRepository $groupRepository,
        OrderItemGroupInterfaceFactory $orderItemGroupFactory,
        UrlHandlerPool $urlHandlerPool,
        DataHelper $dataHelper,
        ConfigHelper $configHelper,
        ItemGroupLinkManagement $linkManagement
    ) {
        $this->linkManagement = $linkManagement;
        parent::__construct(
            $context,
            $customerSession,
            $groupRepository,
            $orderItemGroupFactory,
            $urlHandlerPool,
            $dataHelper,
            $configHelper
        );
    }

    /**
     * @return Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $messages = [];
        $itemId = $this->getRequest()->getParam(OrderItemGroupLinkInterface::SALES_ITEM_ID);
        if (!$itemId) {
            $messages[] = __('Sales order item is not provided');
        }
        $groupId = $this->getRequest()->getParam(OrderItemGroupInterface::GROUP_ID);
        try {
            if (!$groupId) {
                $itemGroup = $this->orderItemGroupFactory->create();
                $itemGroup->setCustomerId($this->customerSession->getCustomerId());
                $itemGroup->setName($this->configHelper->getDefaultGroupName());
            } else {
                $itemGroup = $this->groupRepository->getById($groupId);
            }
            if (!$this->linkManagement->checkRelation($itemGroup->getGroupId(), $itemId)) {
                $this->dataHelper->setUpdatedAt($itemGroup);
                $this->groupRepository->save($itemGroup);
                $this->insertItemGroupLink($itemId, $itemGroup->getGroupId());
            } else {
                $messages['error'] = __('The order item is in the group already');
            }
        } catch (\Exception $exception) {
            $messages['error'] = __('Order item is not added to the group, please try again');
        }
        if (empty($messages)) {
            $messages['success'] = __('Order item has been added to the group');
        }
        $this->_view->loadLayout(self::MYORDERITEMS_INDEX_LAYOUT);
        $layout = $this->_view->getLayout();
        return $this->prepareResult(
            [
                self::RESPONSE_BODY => (string) $layout->renderNonCachedElement(self::ORDER_ITEMS_GROUPS_BLOCK_NAME),
                self::RESPONSE_MESSAGE => $messages
            ]
        );
    }

    /**
     * @param $itemId
     * @param $groupId
     * @throws LocalizedException
     */
    protected function insertItemGroupLink($itemId, $groupId)
    {
        $position = $this->linkManagement->getMinPosition($groupId);
        $this->linkManagement->shiftItems($groupId);
        $this->linkManagement->setItemGroupLink($itemId, $groupId, $position);
    }

    /**
     * @param array $itemsGroupResponse
     * @return \Magento\Framework\Controller\Result\Json
     */
    protected function prepareResult(array $itemsGroupResponse)
    {
        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setData($itemsGroupResponse);
        return $result;
    }
}
