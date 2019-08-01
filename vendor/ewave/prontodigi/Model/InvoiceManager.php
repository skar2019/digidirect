<?php

namespace Ewave\ProntoDigi\Model;

use Ewave\InvoiceIncrementId\Model\IncrementIdUpdater;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Service\InvoiceService;
use Psr\Log\LoggerInterface;
use Ewave\ProntoDigi\ProntoApi\Constants\Order as OrderConst;

class InvoiceManager
{
    /**
     * @var InvoiceService
     */
    protected $invoiceService;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * @var InvoiceSender
     */
    protected $invoiceSender;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var IncrementIdUpdater
     */
    protected $incrementIdUpdater;

    /**
     * InvoiceManager constructor.
     * @param InvoiceService $invoiceService
     * @param InvoiceSender $invoiceSender
     * @param Transaction $transaction
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param IncrementIdUpdater $incrementIdUpdater
     * @param LoggerInterface $logger
     */
    public function __construct(
        InvoiceService $invoiceService,
        InvoiceSender $invoiceSender,
        Transaction $transaction,
        InvoiceRepositoryInterface $invoiceRepository,
        IncrementIdUpdater $incrementIdUpdater,
        LoggerInterface $logger
    ) {
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        $this->invoiceRepository = $invoiceRepository;
        $this->incrementIdUpdater = $incrementIdUpdater;
        $this->logger = $logger;
    }

    /**
     * @param OrderInterface $order
     * @return Order\Invoice|null
     */
    public function createInvoice(OrderInterface $order)
    {
        $invoice = null;
        if ($order->canInvoice()) {
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->register();
            $this->invoiceRepository->save($invoice);
            $this->transaction
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        }
        return $invoice;
    }

    /**
     * @param InvoiceInterface|Order\Invoice $invoice
     * @return bool
     */
    public function notifyCustomer(InvoiceInterface $invoice)
    {
        if ($invoice->getEntityId()) {
            $order = $invoice->getOrder();
            $this->incrementIdUpdater->update($invoice, $order->getData(OrderConst::ATTRIBUTE_PRONTO_ORDER_NUMBER));
            if ($this->invoiceSender->send($invoice)) {
                $order->addCommentToStatusHistory(
                    __('Notified customer about invoice #%1.', $invoice->getIncrementId())
                )
                    ->setIsCustomerNotified(true)
                    ->save();
                    return true;
            }
        }
        return false;
    }
}
