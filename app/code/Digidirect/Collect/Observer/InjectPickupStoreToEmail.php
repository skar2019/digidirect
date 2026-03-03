<?php
namespace Digidirect\Collect\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Digidirect\Collect\Model\StorageHandler;
use Psr\Log\LoggerInterface;

class InjectPickupStoreToEmail implements ObserverInterface
{
    private StorageHandler $storageHandler;
    private LoggerInterface $logger;

    public function __construct(StorageHandler $storageHandler, LoggerInterface $logger)
    {
        $this->storageHandler = $storageHandler;
        $this->logger = $logger;
    }

    public function execute(Observer $observer): void
    {
        try {
            $order = $observer->getEvent()->getOrder();
            if (!$order || $order->getShippingMethod() !== 'collect_collect') {
                return;
            }

            foreach ($order->getAllVisibleItems() as $orderItem) {
                $collectPlaceId = $orderItem->getCollectPlaceId();
                $collectPlaceStorageName = $orderItem->getCollectPlaceStorageName();

                if (!$collectPlaceId || !$collectPlaceStorageName) {
                    continue;
                }

                $collectPlace = $this->storageHandler->getCollectPlaceById(
                    $collectPlaceId,
                    $collectPlaceStorageName
                );

                // if ($collectPlace instanceof \Digidirect\Collect\Api\Data\CollectPlaceInterface) {
                //     $order->setShippingDescription('Click & Collect - ' . $collectPlace->getName());
                //     $order->setData('pickup_store_name', $collectPlace->getName());
                //     $order->setData('pickup_store_address', $collectPlace->getAddress());
                //     break;
                // }

                // if ($collectPlace instanceof \Digidirect\Collect\Api\Data\CollectPlaceInterface) {
                //     $storeName = $collectPlace->getName(); // e.g. "Parramatta"
                //     $order->setShippingDescription('Click & Collect - ' . $storeName);
                //     $order->setData('pickup_store_name', 'digiDirect ' . $storeName); // e.g. "digiDirect Parramatta"
                //     $order->setData('pickup_store_address', $collectPlace->getAddress());
                //     break;
                // }

                // if ($collectPlace instanceof \Digidirect\Collect\Api\Data\CollectPlaceInterface) {
                // $storeName = 'digiDirect ' . $collectPlace->getName();
                // $order->setShippingDescription('Click & Collect - ' . $storeName);
                // $order->setData('pickup_store_name', $storeName);
                // break;
                // }
                
                if ($collectPlace instanceof \Digidirect\Collect\Api\Data\CollectPlaceInterface) {
                    $storeName = 'digiDirect ' . $collectPlace->getName();
                    $order->setShippingDescription('Click & Collect - ' . $storeName);
                    $order->setCustomerNote($storeName);
                    $order->setCustomerNoteNotify(false);
                    break;
                }
                
            }
        } catch (\Exception $e) {
            $this->logger->error('InjectPickupStoreToEmail Error: ' . $e->getMessage());
        }
    }
}