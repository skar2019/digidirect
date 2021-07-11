<?php
namespace Digidirect\CollectAbstractEntity\Plugin\Magento\Sales\Block\Adminhtml\Order\View;

use Magento\Sales\Model\Order\Address;

/**
 * Class Info
 *
 * @package Digidirect\Collect\Block\Plugin\Adminhtml\Order\View
 */
class Info
{
    /**
     * @var \Digidirect\Collect\Model\OrderStoreLocatorInfo
     */
    protected $orderStoreLocatorInfo;

    /**
     * AroundGetItemData constructor.
     *
     * @param \Digidirect\Collect\Model\OrderStoreLocatorInfo $orderStoreLocatorInfo
     */
    public function __construct(
        \Digidirect\Collect\Model\OrderStoreLocatorInfo $orderStoreLocatorInfo,
        \Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory $addressCollection
    ) {
        $this->orderStoreLocatorInfo = $orderStoreLocatorInfo;
        $this->addressCollection = $addressCollection;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View\Info $subject
     * @param \Closure $proceed
     * @param Address $address
     * @return null|string
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetFormattedAddress(
        \Magento\Sales\Block\Adminhtml\Order\View\Info $subject,
        \Closure $proceed,
        Address $address
    ) {
        if ($address->getAddressType() == 'shipping'
            && $storeLocatorItem = $this->orderStoreLocatorInfo->getStoreLocatorItemByOrder($address->getOrder())
        ) {
            $prefix = __('Store: %1 (ID #%2)', $storeLocatorItem->getName(), $storeLocatorItem->getId());
        }
        
        $proceed($address);

        //return (!empty($prefix) ? $prefix . '<br /><br />' : '') . $proceed($address);
        
//        $order = $this->getOrderData($address->getOrder()->sget);
//        $orderBillingId = $order->getBillingAddressId();
//        $address = $this->addressCollection->create()->addFieldToFilter('entity_id',array($orderBillingId))->getFirstItem();
//        return $address;
        
        return (!empty($prefix) ? $prefix . '<br /><br />' : '') . " Testing Address!";
    }
}
