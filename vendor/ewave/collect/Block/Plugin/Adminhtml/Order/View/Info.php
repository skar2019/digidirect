<?php
namespace Ewave\Collect\Block\Plugin\Adminhtml\Order\View;

use Magento\Sales\Model\Order\Address;

/**
 * Class Info
 * @package Ewave\Collect\Block\Plugin\Adminhtml\Order\View
 */
class Info
{
    /**
     * @var \Ewave\Collect\Model\StorageHandler
     */
    protected $_storageHandler;

    /**
     * AroundGetItemData constructor.
     * @param \Ewave\Collect\Model\StorageHandler $_storageHandler
     */
    public function __construct(
        \Ewave\Collect\Model\StorageHandler $_storageHandler
    ) {
        $this->_storageHandler = $_storageHandler;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View\Info $subject
     * @param \Closure $proceed
     * @param Address $address
     * @return null|string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetFormattedAddress(
        \Magento\Sales\Block\Adminhtml\Order\View\Info $subject,
        $proceed,
        Address $address
    ) {
        if ($address->getAddressType() == 'shipping'
            && $storeLocatorInfo = $this->getStoreLocatorInfoByOrder($address->getOrder())) {
            return __('Store ID: %1', nl2br($storeLocatorInfo));
        }

        return $proceed($address);
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\View\Info $subject
     * @param \Closure $proceed
     * @param Address $address
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetAddressEditLink(
        \Magento\Sales\Block\Adminhtml\Order\View\Info $subject,
        $proceed,
        Address $address
    ) {
        if ($address->getAddressType() == 'shipping'
            && $storeLocatorInfo = $this->getStoreLocatorInfoByOrder($address->getOrder())) {
            return '';
        }

        return $proceed($address);
    }

    /**
     * Get Store by Id
     *
     * @param \Magento\Sales\Model\Order $order
     * @return string
     */
    public function getStoreLocatorInfoByOrder($order)
    {
        if ($storeLocatorItem = $this->getStoreLocatorItemByOrder($order)) {
            return nl2br(
                $storeLocatorItem->getId() . "\n" .
                $storeLocatorItem->getName() . "\n" .
                $storeLocatorItem->getAddress()
            );
        }

        return false;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool|\Ewave\Collect\Api\Data\CollectPlaceInterface
     */
    public function getStoreLocatorItemByOrder($order)
    {
        $collectPlaceId = $collectPlaceStorage = false;
        foreach ($order->getAllVisibleItems() as $orderItem) {
            /** @var \Magento\Sales\Model\Order\Item $orderItem **/
            if ($orderItem->getCollectPlaceId()) {
                $collectPlaceId = $orderItem->getCollectPlaceId();
                $collectPlaceStorage = $orderItem->getCollectPlaceStorageName();
                break;
            }
        }

        if ($collectPlaceId) {
            $collectPlace = $this->_storageHandler->getCollectPlaceById(
                $collectPlaceId,
                $collectPlaceStorage
            );
            return $collectPlace && $collectPlace->getId() ? $collectPlace : false;
        }

        return false;
    }
}
