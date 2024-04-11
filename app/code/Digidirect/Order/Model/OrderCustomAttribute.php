<?php

namespace Digidirect\Order\Model;

class OrderCustomAttribute implements Digidirect\Order\Api\OrderCustomAttributeInterface
{
    protected $orderRepository;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    public function getUnitNumber($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        $shippingAddress = $order->getShippingAddress();
        $customAttribute = $shippingAddress->getCustomAttribute('unit_number');
        return $customAttribute ? $customAttribute->getValue() : '';
    }
}
