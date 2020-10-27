<?php

namespace Ewave\ProntoDigi\ProntoApi\Orders\Post;

use Ewave\AI\Helper\Queue as QueueHelper;
use Ewave\AI\Model\Engine\EngineFactory;
use Ewave\ProntoDigi\ProntoApi\Constants\Order as OrderConst;
use Ewave\ProntoDigi\ProntoApi\Orders\Post;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

class OrderPostExecutor
{
    const FAILED_VALIDATION_CODE = 6677;

    /**
     * @var Queue
     */
    protected $queue;

    /**
     * @var EngineFactory
     */
    protected $engineFactory;

    /**
     * OrderPostExecutor constructor.
     * @param QueueHelper $queue
     * @param EngineFactory $engineFactory
     */
    public function __construct(QueueHelper $queue, EngineFactory $engineFactory)
    {
        $this->queue = $queue;
        $this->engineFactory = $engineFactory;
    }

    /**
     * @param OrderInterface $order
     * @throws \Throwable
     * @return void
     */
    public function run(OrderInterface $order)
    {
        $this->validate($order);
        $this->engineFactory->create(
            [
                'processCode' => Post::PROCESS_CODE,
                'initiator' => self::class,
                'runOptions' => [
                    'order' => $order,
                ],
            ]
        )->run();
    }

    /**
     * @param OrderInterface $order
     * @throws \Throwable
     * @return void
     */
    public function pushToQueue(OrderInterface $order)
    {
        $this->validate($order);
        $this->queue->createQueueItem(
            Post::PROCESS_CODE,
            QueueHelper::getSerializedProcessData(['order' => $order->getEntityId()]),
            self::class
        );
    }

    /**
     * @param OrderInterface|Order $order
     * @throws \Throwable
     * @return void
     */
    public function validate(OrderInterface $order)
    {
        $prontoOrderNumber = $order->getData(OrderConst::ATTRIBUTE_PRONTO_ORDER_NUMBER);
        if (!empty($prontoOrderNumber)) {
            throw new LocalizedException(
                __(
                    'Order with increment ID %1 has already sent to Pronto. Pronto order number: %2',
                    $order->getIncrementId(),
                    $prontoOrderNumber
                ),
                null,
                self::FAILED_VALIDATION_CODE
            );
        }
        
        if ($order->getState() == Order::STATE_CANCELED) {
            throw new LocalizedException(
                __('Order with increment ID %1 is canceled and cannot be sent to Pronto', $order->getIncrementId()),
                null,
                self::FAILED_VALIDATION_CODE
            );
        }
        
        $address = $order->getBillingAddress();
        $strt = $address->getStreet();
        
        if (in_array("N/A", $strt)) {
            throw new LocalizedException(
                __('Order with increment ID %1 has no address and cannot be sent to Pronto', $order->getIncrementId()),
                null,
                self::FAILED_VALIDATION_CODE
            );
        }
    }
}
