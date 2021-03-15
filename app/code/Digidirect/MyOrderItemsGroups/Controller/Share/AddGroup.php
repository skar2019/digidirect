<?php

namespace Digidirect\MyOrderItemsGroups\Controller\Share;

use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Sales\Model\Order\Item;
use Magento\Checkout\Controller\Cart as CartController;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as SalesItemCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as ItemCollection;
use Magento\Framework\Exception\LocalizedException;
use Digidirect\MyOrderItemsGroups\Model\ResourceModel\OrderItemGroupLink as ItemGroupLinkResource;

/**
 * Class AddGroup
 * @package Digidirect\MyOrderItemsGroups\Controller\Share
 */
class AddGroup extends CartController
{
    /**
     * Request parameter
     */
    const GROUP_PARAMETER = 'group_id';

    /**
     * @var SalesItemCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var ItemGroupLinkResource
     */
    protected $itemGroupLinkResource;

    /**
     * AddGroupToCart constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param SalesItemCollectionFactory $collectionFactory
     * @param ItemGroupLinkResource $itemGroupLinkResource
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        SalesItemCollectionFactory $collectionFactory,
        ItemGroupLinkResource $itemGroupLinkResource
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->itemGroupLinkResource = $itemGroupLinkResource;
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $salesIds = $this->getSalesIds();
        if (!empty($salesIds)) {
            /* @var $itemsCollection ItemCollection */
            $itemsCollection = $this->collectionFactory->create();
            $itemsCollection->addIdFilter($salesIds);
            $this->addOrderItemCollection($itemsCollection);
        }
        return $this->_goBack();
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    protected function getSalesIds()
    {
        $salesIds = [];
        $groupId = $this->getRequest()->getParam(self::GROUP_PARAMETER);
        try {
            $salesIds = $this->itemGroupLinkResource->getSaleItemIds($groupId);
        } catch (LocalizedException $localizedException) {
            return [];
        }
        return $salesIds;
    }

    /**
     * @param $itemsCollection
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function addOrderItemCollection($itemsCollection)
    {
        /* @var $itemsCollection ItemCollection */
        foreach ($itemsCollection as $item) {
            try {
                $this->addOrderItem($item);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                if ($this->_checkoutSession->getUseNotice(true)) {
                    $this->messageManager->addNoticeMessage($e->getMessage());
                } else {
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $this->_goBack();
            }
        }
        $this->cart->save();
    }

    /**
     * @param Item $item
     */
    protected function addOrderItem(Item $item)
    {
        $this->cart->addOrderItem($item, 1);
        if (!$this->cart->getQuote()->getHasError()) {
            $this->messageManager->addSuccessMessage($this->getMessage());
        }
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getMessage()
    {
        return __(
            'The group has been added to the cart'
        );
    }
}
