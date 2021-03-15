<?php

namespace Digidirect\MyOrderItems\Observer;

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;
use Digidirect\MyOrderItems\Model\OrderItemState;
use Digidirect\MyOrderItems\Model\OrderItemStateRepository;
use Digidirect\MyOrderItems\Api\Data\OrderItemStateInterface;
use Digidirect\MyOrderItems\Api\Data\OrderItemStateInterfaceFactory;

/**
 * Observer should be triggered when new order is created and placed.
 * If Signifyd integration enabled in configuration then new case will be created.
 */
class PlaceOrder implements ObserverInterface
{
    /**
     * Order key
     */
    const ORDER_KEY = 'order';

    /**
     * @var OrderItemStateRepository
     */
    protected $orderItemStateRepository;

    /**
     * @var OrderItemStateInterfaceFactory
     */
    protected $stateFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * PlaceOrder constructor.
     * @param OrderItemStateRepository $orderItemStateRepository
     * @param OrderItemStateInterfaceFactory $stateFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderItemStateRepository $orderItemStateRepository,
        OrderItemStateInterfaceFactory $stateFactory,
        LoggerInterface $logger
    ) {
        $this->orderItemStateRepository = $orderItemStateRepository;
        $this->stateFactory = $stateFactory;
        $this->logger = $logger;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order Order */
        $order = $observer->getEvent()->getData(self::ORDER_KEY);
        if ($order->getCustomerId()) {
            /* @var $saleItem OrderItemInterface */
            foreach ($order->getItems() as $saleItem) {
                if (!$saleItem->getParentItem()) {
                    /* @var $state OrderItemState */
                    $state = $this->stateFactory->create();
                    $state->setSalesItemId($saleItem->getItemId());
                    $state->setStatus(OrderItemStateInterface::ENABLED);
                    $state->setDateOfPurchase($saleItem->getCreatedAt());
                    $state->isObjectNew(true);
                    try {
                        $this->orderItemStateRepository->save($state, $order->getCustomerId());
                    } catch (CouldNotSaveException $exception) {
                        return $this;
                    }
                }
            }
        }
        return $this;
    }
}
