<?php
namespace Ewave\Collect\Model;

/**
 * Class OrderStoreLocatorInfo
 *
 * @package Ewave\Collect\Model
 */
class OrderStoreLocatorInfo
{
    /**
     * @var \Ewave\Collect\Model\StorageHandler
     */
    protected $storageHandler;

    /**
     * @var \Ewave\Collect\Api\Data\CollectPlaceInterface[]|bool[]
     */
    protected $orderCollectPlace = [];

    /**
     * OrderStoreLocatorInfo constructor.
     *
     * @param \Ewave\Collect\Model\StorageHandler $storageHandler
     */
    public function __construct(
        \Ewave\Collect\Model\StorageHandler $storageHandler
    ) {
        $this->storageHandler = $storageHandler;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool|\Ewave\Collect\Api\Data\CollectPlaceInterface
     * @throws \Exception
     */
    public function getStoreLocatorItemByOrder($order)
    {
        if (isset($this->orderCollectPlace[$order->getId()])) {
            return $this->orderCollectPlace[$order->getId()];
        }

        $this->orderCollectPlace[$order->getId()] = false;

        $collectPlaceId = $collectPlaceStorage = false;
        foreach ($order->getAllVisibleItems() as $orderItem) {
            /** @var \Magento\Sales\Model\Order\Item $orderItem * */
            if ($orderItem->getCollectPlaceId()) {
                $collectPlaceId = $orderItem->getCollectPlaceId();
                $collectPlaceStorage = $orderItem->getCollectPlaceStorageName();
                break;
            }
        }

        if ($collectPlaceId) {
            $collectPlace = $this->storageHandler->getCollectPlaceById($collectPlaceId, $collectPlaceStorage);
            $this->orderCollectPlace[$order->getId()] = $collectPlace && $collectPlace->getId() ? $collectPlace : false;
        }

        return $this->orderCollectPlace[$order->getId()];
    }
}
