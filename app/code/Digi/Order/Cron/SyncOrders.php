<?php

namespace Digi\Order\Cron;

use Psr\Log\LoggerInterface;
use Ewave\ProntoDigi\ProntoApi\Orders\Post\OrderPostExecutor;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Ewave\ProntoDigi\ProntoApi\Constants\Order as OrderConst;
use Ewave\AI\Helper\Queue as QueueHelper;
use Ewave\AI\Model\Engine\EngineFactory;
use Ewave\ProntoDigi\ProntoApi\Orders\Post;
use Magento\Sales\Model\ResourceModel\Order\Address\CollectionFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class SyncOrders {

    protected $logger;

    /**
     * @var Magento\Sales\Model\Order
     */
    protected $orderModel;

    /**
     * @var OrderPostExecutor
     */
    protected $orderPostExecutor;

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
    public function __construct(OrderPostExecutor $orderPostExecutor,
            LoggerInterface $logger,
            Order $Order,
            QueueHelper $queue,
            EngineFactory $engineFactory
    ) {
        $this->logger = $logger;
        $this->orderPostExecutor = $orderPostExecutor;
        $this->orderModel = $Order;
        $this->queue = $queue;
        $this->engineFactory = $engineFactory;
    }

    /**
     * Push to Pronto Que
     *
     * 
     */
    public function execute() {
        try {
            $date = date("Y-m-d", strtotime('2020-10-22'));
            $orders = $this->orderModel->getCollection()
                    ->addAttributeToFilter('pronto_order_number', ['null' => true])
                    ->addAttributeToFilter('status', ['neq' => 'canceled'])
                    ->addAttributeToFilter('created_at', ['from' => $date]);

            foreach ($orders as $key => $order) {
                $address = $order->getBillingAddress();
                $strt = $address->getStreet();
                $condition = in_array("N/A", $strt);
                if ($condition) {
                    $this->logger->error('Order with increment ID has no address and cannot be sent to Pronto: ' . $order->getIncrementId()); 
                    continue;
                } else {
                    $this->engineFactory->create(
                            [
                                'processCode' => Post::PROCESS_CODE,
                                'initiator' => self::class,
                                'runOptions' => [
                                    'order' => $order,
                                ],
                            ]
                    )->run();
                    $this->logger->info($order->getEntityId());
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('Order Sync Cron', ['path' => __METHOD__, 'exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

}
