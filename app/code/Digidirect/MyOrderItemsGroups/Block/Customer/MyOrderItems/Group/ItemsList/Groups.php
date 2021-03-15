<?php

namespace Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group\ItemsList;

use Magento\Sales\Model\ResourceModel\Order\Item\Collection as ItemCollection;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\Context;
use Digidirect\MyOrderItems\Helper\Config as ConfigHelper;
use Digidirect\MyOrderItems\Helper\Data as DataHelper;
use Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group\AbstractBlock;
use Digidirect\MyOrderItemsGroups\Model\OrderItemGroupRepository;
use Digidirect\MyOrderItemsGroups\Model\ItemGroupLinkRepository;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupInterface;
use Digidirect\MyOrderItemsGroups\Api\Data\OrderItemgroupCollectionInterface;
use Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group\ItemsList;
use Digidirect\MyOrderItems\Model\UrlHandlerPool;

/**
 * Class Groups
 * @package Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\ItemsList
 */
class Groups extends ItemsList
{
    /**
     * Const category name
     */
    const NAME = 'name';

    /**
     * @var array
     */
    protected $groups = [];

    /**
     * @var string
     */
    protected $groupName = '';

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var OrderItemGroupInterface[]
     */
    protected $groupCollection;

    /**
     * Groups constructor.
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param ConfigHelper $configHelper
     * @param OrderItemGroupRepository $groupRepository
     * @param ItemGroupLinkRepository $groupLinkRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param Session $customerSession
     * @param UrlHandlerPool $urlHandlerPool
     * @param array $data
     */
    public function __construct(
        Context $context,
        DataHelper $dataHelper,
        ConfigHelper $configHelper,
        OrderItemGroupRepository $groupRepository,
        ItemGroupLinkRepository $groupLinkRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        Session $customerSession,
        UrlHandlerPool $urlHandlerPool,
        array $data = []
    ) {
        $this->sortOrderBuilder = $sortOrderBuilder;
        parent::__construct(
            $context,
            $dataHelper,
            $configHelper,
            $groupRepository,
            $groupLinkRepository,
            $searchCriteriaBuilder,
            $customerSession,
            $urlHandlerPool,
            $data = []
        );
        $this->initializeGroups();
    }

    /**
     * @param ItemCollection $collection
     */
    public function setCollection(ItemCollection $collection)
    {
        $groupId = $this->getCurrentGroupId();
        if ($groupId) {
            $this->groupLinkRepository->filterGroups($collection, $groupId);
        }
    }

    /**
     * return void
     */
    protected function initializeGroups()
    {
        foreach ($this->getGroupItems() as $group) {
            $this->groups[$group->getGroupId()] = $group->getName();
            if ($group->getGroupId() == $this->getCurrentGroupId()) {
                $this->groupName = $group->getName();
            }
        }
    }

    /**
     * @return OrderItemGroupInterface[]
     */
    protected function getGroupItems()
    {
        if (!$this->groupCollection) {
            $sortOrder = $this->sortOrderBuilder
                ->setField(OrderItemGroupInterface::UPDATED_AT)
                ->setDirection(SortOrder::SORT_DESC)
                ->create();
            $this->searchCriteriaBuilder
                ->addFilter(OrderItemGroupInterface::CUSTOMER_ID, $this->getCustomerId())
                ->addSortOrder($sortOrder);
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $searchResult = $this->groupRepository->getOrderItemGroupList($searchCriteria);
            $this->groupCollection = $searchResult->getItems();
        }
        return $this->groupCollection;
    }

    /**
     * @return string
     */
    public function getCurrentGroupName()
    {
        return $this->groupName;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return bool
     */
    public function isRename()
    {
        return $this->getCurrentGroupName() == $this->getConfigHelper()->getDefaultGroupName();
    }
}
