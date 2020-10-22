<?php

namespace Digi\Order\Cron;

use Psr\Log\LoggerInterface;
use Ewave\ProntoDigi\ProntoApi\Orders\Post\OrderPostExecutor;
use Magento\Sales\Model\Order;

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

    public function __construct(OrderPostExecutor $orderPostExecutor,
            LoggerInterface $logger,
            Order $Order) {
        $this->logger = $logger;
        $this->orderPostExecutor = $orderPostExecutor;
        $this->orderModel = $Order;
    }

    /**
     * Push to Pronto Que
     *
     * 
     */
    public function execute() {
        $date = date("Y-m-d",strtotime('2020-10-22'));
        $orders = $this->orderModel->getCollection()
                ->addAttributeToFilter('pronto_order_number', ['null' => true])
                ->addAttributeToFilter('created_at',['from' => $date]);

        foreach ($orders as $key => $order) {
            $this->orderPostExecutor->pushToQueue($order);
            $this->logger->info('Pronto Order Sync Runs');
        }
    }

}
