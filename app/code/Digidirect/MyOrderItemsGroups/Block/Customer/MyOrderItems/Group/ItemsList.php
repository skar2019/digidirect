<?php

namespace Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group;

use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use Magento\Customer\Model\Session;
use Digidirect\MyOrderItemsGroups\Model\ItemGroupLinkRepository;
use Digidirect\MyOrderItems\Helper\Data as DataHelper;
use Digidirect\MyOrderItems\Helper\Config as ConfigHelper;
use Digidirect\MyOrderItemsGroups\Model\OrderItemGroupRepository;
use Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group\ItemsList\Pager as GroupsPager;
use Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group\ItemsList\Groups;
use Digidirect\MyOrderItems\Model\UrlHandlerPool;

/**
 * Class ItemsList
 * @package Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group
 */
class ItemsList extends AbstractBlock
{
    /**
     * @var int
     */
    protected $itemsPerPage;

    /**
     * @var int|null
     */
    protected $currentGroupId;

    /**
     * @var OrderItemCollection
     */
    protected $itemCollection;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var array
     */
    protected $itemIds = [];

    /**
     * ItemsList constructor.
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param ConfigHelper $configHelper
     * @param OrderItemGroupRepository $groupRepository
     * @param ItemGroupLinkRepository $groupLinkRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
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
        Session $customerSession,
        UrlHandlerPool $urlHandlerPool,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct(
            $context,
            $dataHelper,
            $configHelper,
            $groupRepository,
            $groupLinkRepository,
            $searchCriteriaBuilder,
            $urlHandlerPool,
            $data = []
        );
    }

    /**
     * @return Template
     */
    protected function _prepareLayout()
    {
        try {
            $currentGroupId = $this->getCurrentGroupId();
        } catch (LocalizedException $exception) {
            return parent::_prepareLayout();
        }
        if ($currentGroupId) {
            $this->itemsPerPage = $this->configHelper->getItemsPerPage();
            $this->itemCollection = $this->getList();
            /** @var Groups $groupsBlock */
            $groupsBlock = $this->getChildBlock('my_order_item_groups');
            if ($groupsBlock) {
                $groupsBlock->setCollection($this->itemCollection);
            }
            $this->setItemIds();
            /** @var GroupsPager $pagerBlock */
            $pagerBlock = $this->getChildBlock('my_order_item_group_pager');
            if ($pagerBlock) {
                $pagerBlock->setLimit($this->itemsPerPage);
                //here pager updates collection parameters
                $pagerBlock->setCollection($this->itemCollection);
                $pagerBlock->setAvailableLimit([$this->itemsPerPage]);
                $pagerBlock->setShowAmounts($this->isPagerDisplayed());
            }
        }
        return parent::_prepareLayout();
    }

    /**
     * @return bool
     */
    public function isPagerDisplayed()
    {
        $pagerBlock = $this->getChildBlock('my_order_item_group_pager');
        return $pagerBlock && ($this->itemCollection->getSize() > $this->itemsPerPage);
    }

    /**
     * @return bool
     */
    public function isGroupFilterDisplayed()
    {
        $pagerBlock = $this->getChildBlock('my_order_item_groups');
        return $pagerBlock && $this->currentGroupId;
    }

    /**
     * @return OrderItemCollection
     */
    public function getList()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->groupLinkRepository->getOrderItemList($searchCriteria, $this->getCustomerId());
    }

    /**
     * @return OrderItemCollection
     */
    public function getItems()
    {
        return $this->itemCollection;
    }

    /**
     * @return array
     */
    public function getItemIds()
    {
        return $this->dataHelper->jsonEncode($this->itemIds);
    }

    /**
     * @return string
     */
    public function setItemIds()
    {
        $itemIds = [];
        $items = clone $this->itemCollection;
        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($items as $item) {
            $itemIds[] = $item->getId();
        }
        $this->itemIds = $itemIds;
    }

    /**
     * @return int|null
     */
    public function getCurrentGroupId()
    {
        if (!$this->currentGroupId) {
            $this->currentGroupId = $this->locateCurrentGroupId();
        }
        return $this->currentGroupId;
    }

    /**
     * @return int|null
     */
    protected function locateCurrentGroupId()
    {
        $groupId = (int)$this->getRequest()->getParam($this->getGroupVarName());
        $customerId = $this->getCustomerId();
        if ($groupId) {
            if (!$this->groupRepository->checkGroupForCustomer($customerId, $groupId)) {
                throw new LocalizedException(__('You are not allowed to see this group'));
            }
            return $groupId;
        }
        $groupId = $this->groupRepository->getCurrentGroup($customerId);
        if ($groupId) {
            return $groupId;
        }
        return null;
    }

    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }

    /**
     * @return string
     */
    public function getGroupVarName()
    {
        return self::GROUP_PARAMETER;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('my_order_item_group_pager');
    }

    /**
     * @return string
     */
    public function getGroupFilterHtml()
    {
        return $this->getChildHtml('my_order_item_groups');
    }

    /**
     * @return bool
     */
    public function isEmptyGroup()
    {
        return !$this->getCurrentGroupId();
    }

    /**
     * @return string
     */
    public function getFormRenameActionUrl()
    {
        return $this->_urlBuilder->getUrl('myorderitems/myorderitems/rename', $this->collectFilterParams());
    }

    /**
     * @return string
     */
    public function getFormClearActionUrl()
    {
        return $this->_urlBuilder->getUrl('myorderitems/myorderitems/clear', $this->collectFilterParams());
    }

    /**
     * @param array $params
     * @return string
     */
    public function getIndexUrl(array $params = [])
    {
        return $this->_urlBuilder->getUrl('myorderitems/myorderitems/index', $params);
    }

    /**
     * @return string
     */
    public function getAddItemUrl()
    {
        return $this->_urlBuilder->getUrl('myorderitems/myorderitems/additem');
    }
}
