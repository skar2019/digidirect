<?php

namespace Ewave\ProntoDigi\Model;

use Ewave\ProntoDigi\Api\ApiOrderInterface;
use Ewave\ProntoDigi\ProntoApi\Orders\Get\OrderGetExecutor;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class ApiOrder implements ApiOrderInterface
{
    /**
     * @var OrderGetExecutor
     */
    protected $orderGetExecutor;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ApiOrder constructor.
     * @param OrderGetExecutor $orderGetExecutor
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderGetExecutor $orderGetExecutor,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger
    ) {
        $this->orderGetExecutor = $orderGetExecutor;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * @param OrderInterface $entity
     * @return OrderInterface
     * @throws \Throwable
     */
    public function update(OrderInterface $entity)
    {
        try {
            /** @var Order $order */
            $order = $this->orderRepository->get($entity->getEntityId());
            $order->addData($entity->getData());
            $order = $this->orderRepository->save($order);
        } catch (\Throwable $e) {
            $this->logger->critical(__('Could not update order: %1', $e->__toString()));
            throw $e;
        }

        try {
            $this->orderGetExecutor->pushToQueue($order);
        } catch (\Throwable $e) {
            $this->logger->critical(__('Order Detail Update: cannot add order to queue: %1', $e->__toString()));
            throw $e;
        }
        return $order;
    }
}
