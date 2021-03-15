<?php

namespace Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group;

use Digidirect\MyOrderItems\Helper\Config as ConfigHelper;
use Digidirect\MyOrderItems\Helper\Data as DataHelper;
use Digidirect\MyOrderItems\Model\UrlHandlerPool;
use Digidirect\MyOrderItemsGroups\Model\ItemGroupLinkRepository;
use Digidirect\MyOrderItemsGroups\Model\OrderItemGroupRepository;
use Magento\Catalog\Block\Product\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\ActionInterface;
use Magento\Sales\Model\Order\Item as SalesItem;

/**
 * Class ShareList
 * @package Digidirect\MyOrderItemsGroups\Block\Customer\MyOrderItems\Group
 */
class Share extends ItemsList
{
    /**
     * Request parameter
     */
    const ITEM_PARAMETER = 'sales_item_id';

    /**
     * @var \Digidirect\MyOrderItemsGroups\Model\OrderItemGroup
     */
    protected $currentGroup;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * Share constructor.
     * @param Context $context
     * @param DataHelper $dataHelper
     * @param ConfigHelper $configHelper
     * @param OrderItemGroupRepository $groupRepository
     * @param ItemGroupLinkRepository $groupLinkRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Session $customerSession
     * @param UrlHandlerPool $urlHandlerPool
     * @param array $data
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
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
        array $data = [],
        \Magento\Framework\Api\FilterBuilder $filterBuilder
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->wishlistHelper = $context->getWishlistHelper();
        parent::__construct(
            $context,
            $dataHelper,
            $configHelper,
            $groupRepository,
            $groupLinkRepository,
            $searchCriteriaBuilder,
            $customerSession,
            $urlHandlerPool,
            $data
        );
    }

    /**
     * @return \Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupInterface|null
     */
    public function getCurrentGroup()
    {
        if (!$this->currentGroup) {
            try {
                $this->currentGroup = $this->groupRepository->getById($this->getCurrentGroupId());
            } catch (NoSuchEntityException $exception) {
                return null;
            }
        }
        return $this->currentGroup;
    }

    /**
     * @return int|null
     */
    protected function locateCurrentGroupId()
    {
        return $this->getRequest()->getParam(self::GROUP_PARAMETER);
    }

    /**
     * @return string
     */
    public function getAddGroupActionUrl()
    {
        return $this->_urlBuilder->getUrl(
            'myorderitems/share/addgroup',
            [self::GROUP_PARAMETER => $this->getCurrentGroupId()]
        );
    }

    /**
     * @param $salesItemId
     * @return string
     */
    public function getAddItemActionUrl($salesItemId)
    {
        return $this->_urlBuilder->getUrl(
            'myorderitems/share/additem',
            [self::ITEM_PARAMETER => $salesItemId]
        );
    }

    /**
     * @param SalesItem $salesItem
     * @return mixed
     */
    public function isItemSalable(SalesItem $salesItem)
    {
        return $this->dataHelper->isItemSalable($salesItem);
    }

    /**
     * @return OrderItemCollection
     */
    public function getList()
    {
        $selectFilter = $this->filterBuilder
            ->setField(\Digidirect\MyOrderItemsGroups\Api\Data\OrderItemGroupInterface::GROUP_ID)
            ->setConditionType('eq')
            ->setValue($this->getCurrentGroupId())
            ->create();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilters([$selectFilter])
            ->create();
        return $this->groupLinkRepository->getOrderItemList($searchCriteria);
    }

    /**
     * Return HTML block with tier price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        $price = '';

        if ($priceRender) {
            $price = $priceRender->render($priceType, $product, $arguments);
        }
        return $price;
    }

    /**
     * @param SalesItem $salesItem
     * @return \Magento\Catalog\Api\Data\ProductInterface|\Magento\Catalog\Model\Product|null
     */
    public function getProduct(SalesItem $salesItem)
    {
        return $this->dataHelper->getProduct($salesItem);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getCurrentGroupId()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Retrieve add to wishlist params
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToWishlistParams($product)
    {
        return $this->getWishlistHelper()->getAddParams($product);
    }

    /**
     * @return \Magento\Wishlist\Helper\Data
     */
    public function getWishlistHelper()
    {
        return $this->wishlistHelper;
    }
}
