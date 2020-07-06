<?php

namespace Ewave\ProntoDigi\Model;

use Ewave\ProntoDigi\Api\ApiOrderInterface;
use Ewave\ProntoDigi\ProntoApi\Orders\Get\OrderGetExecutor;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Ewave\ProntoDigi\Helper\Config;

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
     * @var InvoiceManager
     */
    protected $invoiceManager;
    /**
     * @var Config
     */
    private $config;

    /**
     * ApiOrder constructor.
     * @param OrderGetExecutor $orderGetExecutor
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     * @param InvoiceManager $invoiceManager
     * @param Config $config
     */
    public function __construct(
        OrderGetExecutor $orderGetExecutor,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        InvoiceManager $invoiceManager,
        Config $config
    ) {
        $this->orderGetExecutor = $orderGetExecutor;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
        $this->invoiceManager = $invoiceManager;
        $this->config = $config;
    }

    /**
     * @param OrderInterface $entity
     * @return OrderInterface
     * @throws \Throwable
     */
    public function update(OrderInterface $entity)
    {
        $this->debugEntity($entity);
        $order = $this->changeOrder($entity);
        $invoice = $this->createInvoice($order, $entity);
        $this->notifyCustomer($invoice, $entity);
        return $order;
    }

    /**
     * @param OrderInterface $entity
     * @return void
     */
    public function debugEntity(OrderInterface $entity)
    {
        if ($this->config->getDebugUpdateRequest()) {
            $this->logger->info(
                'Update Request data from Pronto : ',
                ['Entity Data: ' => $entity->getData()]
            );
        }
    }

    /**
     * @param OrderInterface $entity
     * @return OrderInterface Order
     * @throws \Throwable
     */
    public function changeOrder(OrderInterface $entity)
    {
        try {
            /** @var Order $order */
            $order = $this->orderRepository->get($entity->getEntityId());
            $order->addData($entity->getData());
            $order = $this->orderRepository->save($order);
        } catch (\Throwable $e) {
            $this->logger->critical(
                __('The order can not be saved error: %1', $e->__toString()),
                ['Entity Data: ' => $entity->getData()]
            );
            throw $e;
        }
        return $order;
    }

    /**
     * @param OrderInterface $order
     * @param OrderInterface $entity
     * @return InvoiceInterface|Order\Invoice
     * @throws \Throwable
     */
    public function createInvoice(OrderInterface $order, OrderInterface $entity)
    {
        try {
            /** @var \Magento\Sales\Model\Order\Invoice $invoice */
            $invoice = $order->getInvoiceCollection()->getFirstItem();
            $invoice = $invoice->getEntityId() ? $invoice : $this->invoiceManager->createInvoice($order);
            if ($invoice === null) {
                throw new LocalizedException(__('Invoice not created by order ID %1', $order->getIncrementId()));
            }
        } catch (\Throwable $e) {
            $this->logger->critical(
                __('There was an error creating invoice: %1', $e->__toString()),
                [
                    'Entity Data: ' => $entity->getData(),
                    'Order ID: ' => $order->getIncrementId()
                ]
            );
            throw $e;
        }
        return $invoice;
    }

    /**
     * @param InvoiceInterface|Order\Invoice $invoice
     * @param OrderInterface $entity
     * @return void
     */
    public function notifyCustomer(InvoiceInterface $invoice, OrderInterface $entity)
    {
        try {
            $this->invoiceManager->notifyCustomer($invoice);
        } catch (\Throwable $e) {
            $this->logger->critical(
                __('Couldn\'t send email invoice: %1. Error: ', $e->__toString()),
                [
                    'Entity Data: ' => $entity->getData(),
                    'Invoice ID: ' => $invoice->getId()
                ]
            );
        }
    }
}
