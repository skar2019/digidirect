<?php
declare(strict_types=1);

namespace Digidirect\Collect\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Digidirect\Collect\Model\StorageHandler;
use Psr\Log\LoggerInterface;

class InjectPickupStoreToEmail implements ObserverInterface
{
    /**
     * @var StorageHandler
     */
    private StorageHandler $storageHandler;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(
        StorageHandler $storageHandler,
        LoggerInterface $logger
    ) {
        $this->storageHandler = $storageHandler;
        $this->logger         = $logger;
    }

    public function execute(Observer $observer): void
    {
        try {
            $order = $observer->getEvent()->getOrder();

            if (!$order || $order->getShippingMethod() !== 'collect_collect') {
                return;
            }

            $storeName    = null;
            $storeAddress = null;

            // Loop through order items to find the collect place
            foreach ($order->getAllVisibleItems() as $orderItem) {
                $collectPlaceId          = $orderItem->getCollectPlaceId();
                $collectPlaceStorageName = $orderItem->getCollectPlaceStorageName();

                if (!$collectPlaceId || !$collectPlaceStorageName) {
                    continue;
                }

                // Use the existing StorageHandler — same as admin tab uses
                $collectPlace = $this->storageHandler->getCollectPlaceById(
                    $collectPlaceId,
                    $collectPlaceStorageName
                );

                if ($collectPlace instanceof \Digidirect\Collect\Api\Data\CollectPlaceInterface) {
                    $storeName    = $collectPlace->getName();
                    $storeAddress = $collectPlace->getAddress();
                    break; // Use first item's store
                }
            }

            if ($storeName) {
                // Update shipping description so {{var order.shipping_description}} works in email
                $order->setShippingDescription('Click & Collect - ' . $storeName);
                $order->setData('pickup_store_name', $storeName);
                $order->setData('pickup_store_address', $storeAddress);

                $this->logger->info(
                    'InjectPickupStoreToEmail: Order ' . $order->getIncrementId() .
                    ' store = ' . $storeName
                );
            }

        } catch (\Exception $e) {
            $this->logger->error('InjectPickupStoreToEmail Error: ' . $e->getMessage());
        }
    }
}
