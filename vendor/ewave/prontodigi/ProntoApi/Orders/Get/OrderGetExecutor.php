<?php

namespace Ewave\ProntoDigi\ProntoApi\Orders\Get;

use Ewave\AI\Helper\Queue as QueueHelper;
use Ewave\AI\Model\Engine\EngineFactory;
use Ewave\ProntoDigi\ProntoApi\Orders\Get;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

class OrderGetExecutor
{
    /**
     * @var QueueHelper
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
        $this->engineFactory->create(
            [
                'processCode' => Get::PROCESS_CODE,
                'initiator' => self::class,
                'runOptions' => [
                    'order' => $order,
                ],
            ]
        )->run();
    }

    /**
     * @param OrderInterface|Order $order
     * @throws \Throwable
     * @return void
     */
    public function pushToQueue(OrderInterface $order)
    {
        $this->queue->createQueueItem(
            Get::PROCESS_CODE,
            QueueHelper::getSerializedProcessData(
                ['order' => $order->getEntityId()]
            ),
            self::class
        );
    }
}
