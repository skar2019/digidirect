<?php

namespace Digidirect\MyOrderItems\Block\Customer\MyOrderItems;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use Digidirect\MyOrderItems\Model\OrderItemStateRepository;
use Digidirect\MyOrderItems\Helper\Data as DataHelper;
use Digidirect\MyOrderItems\Helper\Config as ConfigHelper;
use Digidirect\MyOrderItems\Model\UrlHandlerPool;

/**
 * Class
 * @package Digidirect\MyOrderItems\Block\MyOrderItems
 */
class ItemsList extends AbstractBlock
{
    /**
     * @var int
     */
    protected $itemsPerPage;

    /**
     * @var OrderItemCollection
     */
    protected $itemCollection;

    /**
     * @var OrderItemStateRepository
     */
    protected $stateRepository;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * ItemsList constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataHelper $dataHelper
     * @param ConfigHelper $configHelper
     * @param UrlHandlerPool $urlHandlerPool
     * @param OrderItemStateRepository $stateRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataHelper $dataHelper,
        ConfigHelper $configHelper,
        UrlHandlerPool $urlHandlerPool,
        OrderItemStateRepository $stateRepository,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->stateRepository = $stateRepository;
        parent::__construct(
            $context,
            $dataHelper,
            $configHelper,
            $urlHandlerPool,
            $data = []
        );
    }

    /**
     * @return Template
     */
    protected function _prepareLayout()
    {
        $this->itemsPerPage = $this->configHelper->getItemsPerPage();
        $this->itemCollection = $this->getList();
        /** @var \Digidirect\MyOrderItems\Block\Customer\MyOrderItems\ItemsList\Categories $categoriesBlock */
        $categoriesBlock = $this->getChildBlock('my_order_item_categories');
        if ($categoriesBlock) {
            //here categories filter updates collection parameters
            $categoriesBlock->setCollection($this->itemCollection);
        }
        /** @var \Magento\Theme\Block\Html\Pager $pagerBlock */
        $pagerBlock = $this->getChildBlock('my_order_item_pager');
        if ($pagerBlock) {
            $pagerBlock->setLimit($this->itemsPerPage);
            //here pager updates collection parameters
            $pagerBlock->setCollection($this->itemCollection);
            $pagerBlock->setAvailableLimit([$this->itemsPerPage]);
            $pagerBlock->setShowAmounts($this->isPagerDisplayed());
        }
        return parent::_prepareLayout();
    }

    /**
     * @return bool
     */
    public function isPagerDisplayed()
    {
        $pagerBlock = $this->getChildBlock('my_order_item_pager');
        return $pagerBlock && ($this->itemCollection->getSize() > $this->itemsPerPage);
    }

    /**
     * @return OrderItemCollection
     */
    public function getList()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $myOrderItems = $this->stateRepository->getOrderItemList(
            $searchCriteria,
            $this->getCustomerId()
        );
        return $myOrderItems;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('my_order_item_pager');
    }

    /**
     * @return string
     */
    public function getCategoryFilterHtml()
    {
        return $this->getChildHtml('my_order_item_categories');
    }

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getItems()
    {
        return $this->itemCollection->getItems();
    }

    /**
     * @return int|null
     */
    protected function getCustomerId()
    {
        return $this->customerSession->getCustomerId();
    }
}
