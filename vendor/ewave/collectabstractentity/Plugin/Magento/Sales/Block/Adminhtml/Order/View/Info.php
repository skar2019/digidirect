<?php
namespace Ewave\CollectAbstractEntity\Plugin\Magento\Sales\Block\Adminhtml\Order\View;

use Magento\Sales\Model\Order\Address;

/**
 * Class Info
 *
 * @package Ewave\Collect\Block\Plugin\Adminhtml\Order\View
 */
class Info
{
    /**
     * @var \Ewave\Collect\Model\OrderStoreLocatorInfo
     */
    protected $orderStoreLocatorInfo;

    /**
     * AroundGetItemData constructor.
     *
     * @param \Ewave\Collect\Model\OrderStoreLocatorInfo $orderStoreLocatorInfo
     */
    public function __construct(
        \Ewave\Collect\Model\OrderStoreLocatorInfo $orderStoreLocatorInfo
    ) {
        $this->orderStoreLocatorInfo = $orderStoreLocatorInfo;
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

        return (!empty($prefix) ? $prefix . '<br /><br />' : '') . $proceed($address);
    }
}
